<?php

namespace App\Actions\Merchants;

use Illuminate\Support\Str;

class GenerateMerchantCredentials
{
  /**
   * Generate a unique API key for a merchant.
   * Format: cs_live_<32 random hex chars>
   */
  public function generateApiKey(): string
  {
    return 'cs_live_' . bin2hex(random_bytes(24));
  }

  /**
   * Generate a webhook signing secret.
   * Format: whsec_<32 random hex chars>
   */
  public function generateWebhookSecret(): string
  {
    return 'whsec_' . bin2hex(random_bytes(24));
  }
}
