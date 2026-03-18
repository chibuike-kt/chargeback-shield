<?php

namespace App\Services;

use App\Models\EvidenceBundle;
use App\Models\Merchant;
use RuntimeException;

class EvidenceService
{
  private const CIPHER = 'AES-256-CBC';

    // ── Encryption ────────────────────────────────────────────────────────────

  /**
   * Encrypt a payload array using AES-256-CBC.
   * Returns the base64-encoded ciphertext and the base64-encoded IV.
   */
  public function encrypt(array $payload, string $encryptionKey): array
  {
    $iv         = random_bytes(16);
    $key        = $this->deriveKey($encryptionKey);
    $plaintext  = json_encode($payload);
    $ciphertext = openssl_encrypt($plaintext, self::CIPHER, $key, OPENSSL_RAW_DATA, $iv);

    if ($ciphertext === false) {
      throw new RuntimeException('Evidence bundle encryption failed.');
    }

    return [
      'encrypted' => base64_encode($ciphertext),
      'iv'        => base64_encode($iv),
    ];
  }

  /**
   * Decrypt a stored evidence bundle payload.
   */
  public function decrypt(string $encryptedPayload, string $iv, string $encryptionKey): array
  {
    $key       = $this->deriveKey($encryptionKey);
    $plaintext = openssl_decrypt(
      base64_decode($encryptedPayload),
      self::CIPHER,
      $key,
      OPENSSL_RAW_DATA,
      base64_decode($iv)
    );

    if ($plaintext === false) {
      throw new RuntimeException('Evidence bundle decryption failed. Bundle may be corrupted.');
    }

    return json_decode($plaintext, true);
  }

    // ── Signing ───────────────────────────────────────────────────────────────

  /**
   * Generate an HMAC-SHA256 signature over the evidence payload.
   * Uses the merchant's webhook secret as the signing key.
   */
  public function sign(array $payload, string $signingKey): string
  {
    // Canonical form: sort keys for deterministic signing
    $canonical = json_encode($this->sortArrayRecursively($payload));

    return hash_hmac('sha256', $canonical, $signingKey);
  }

  /**
   * Verify the HMAC-SHA256 signature of a decrypted bundle.
   * Returns true if the signature is valid.
   */
  public function verify(array $payload, string $signature, string $signingKey): bool
  {
    $expected = $this->sign($payload, $signingKey);

    return hash_equals($expected, $signature);
  }

    // ── Bundle retrieval ──────────────────────────────────────────────────────

  /**
   * Retrieve and decrypt an evidence bundle.
   * Returns the decrypted payload and verification status.
   */
  public function retrieveBundle(EvidenceBundle $bundle, Merchant $merchant): array
  {
    // Decrypt the payload
    $payload = $this->decrypt(
      $bundle->payload_encrypted,
      $bundle->encryption_iv,
      $merchant->webhook_secret
    );

    // Verify the signature
    $isValid = $this->verify($payload, $bundle->hmac_signature, $merchant->webhook_secret);

    // Update verification status if it changed
    if ($bundle->is_verified !== $isValid) {
      // We can't use save() — bundle is immutable
      // Use query builder directly to update only the verification flag
      EvidenceBundle::where('id', $bundle->id)
        ->update(['is_verified' => $isValid]);
    }

    return [
      'bundle_id'          => $bundle->ulid,
      'transaction_id'     => $bundle->transaction->ulid,
      'signature_valid'    => $isValid,
      'hmac_signature'     => $bundle->hmac_signature,
      'created_at'         => $bundle->created_at->toIso8601String(),
      'payload'            => $payload,
    ];
  }

    // ── Helpers ───────────────────────────────────────────────────────────────

  /**
   * Derive a 32-byte key from the provided secret using SHA-256.
   * This ensures the key is always the right length for AES-256.
   */
  private function deriveKey(string $secret): string
  {
    return hash('sha256', $secret, true);
  }

  /**
   * Recursively sort array keys for deterministic JSON encoding.
   * Required for consistent HMAC signing.
   */
  private function sortArrayRecursively(array $data): array
  {
    ksort($data);

    foreach ($data as $key => $value) {
      if (is_array($value)) {
        $data[$key] = $this->sortArrayRecursively($value);
      }
    }

    return $data;
  }
}
