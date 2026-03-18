<?php

namespace App\Services;

class ReasonCodeRegistry
{
  /**
   * Full reason code definitions for Visa and Mastercard.
   * Each entry contains the code, description, category,
   * required evidence, and response strategy.
   */
  private array $visaCodes = [
    '10.1' => [
      'code'              => '10.1',
      'network'           => 'visa',
      'description'       => 'EMV Liability Shift Counterfeit Fraud',
      'category'          => 'Fraud',
      'time_limit_days'   => 120,
      'required_evidence' => [
        'device_fingerprint',
        'ip_address',
        'transaction_timestamp',
        'risk_score',
      ],
      'response_strategy' => 'Submit cryptographic evidence of card-present authentication including device fingerprint, IP geolocation data, and risk score at time of authorization. Demonstrate that all fraud signals were within acceptable thresholds.',
      'winning_argument'  => 'Transaction was processed with full risk assessment. Device fingerprint matched known pattern. Geolocation was consistent with cardholder\'s registered region.',
    ],
    '10.4' => [
      'code'              => '10.4',
      'network'           => 'visa',
      'description'       => 'Other Fraud – Card Absent Environment',
      'category'          => 'Fraud',
      'time_limit_days'   => 120,
      'required_evidence' => [
        'ip_address',
        'device_fingerprint',
        'session_data',
        'risk_score',
        'velocity_signals',
      ],
      'response_strategy' => 'Provide evidence that the transaction was initiated from a known device with a valid session. Include IP geolocation, device fingerprint, session age, and risk score. Show that no velocity anomalies were detected.',
      'winning_argument'  => 'Card-not-present transaction was authenticated through multi-signal risk assessment. No velocity flags or geographic anomalies were detected at transaction time.',
    ],
    '10.5' => [
      'code'              => '10.5',
      'network'           => 'visa',
      'description'       => 'Visa Fraud Monitoring Program',
      'category'          => 'Fraud',
      'time_limit_days'   => 120,
      'required_evidence' => [
        'transaction_history',
        'risk_score',
        'device_fingerprint',
      ],
      'response_strategy' => 'Demonstrate that transaction patterns were within normal bounds for this cardholder. Provide full risk signal breakdown and show that no monitoring thresholds were exceeded.',
      'winning_argument'  => 'Transaction history shows consistent patterns. Risk scoring at time of transaction was below fraud threshold with all signals within normal ranges.',
    ],
    '11.1' => [
      'code'              => '11.1',
      'network'           => 'visa',
      'description'       => 'Card Recovery Bulletin',
      'category'          => 'Authorization',
      'time_limit_days'   => 75,
      'required_evidence' => [
        'authorization_record',
        'risk_score',
      ],
      'response_strategy' => 'Provide authorization approval record with timestamp and risk assessment data. Show that card was not flagged at time of transaction.',
      'winning_argument'  => 'Card was not listed in any recovery bulletin at time of authorization. Full authorization record with timestamp is provided.',
    ],
    '12.5' => [
      'code'              => '12.5',
      'network'           => 'visa',
      'description'       => 'Incorrect Transaction Amount',
      'category'          => 'Processing Error',
      'time_limit_days'   => 120,
      'required_evidence' => [
        'transaction_record',
        'amount_confirmation',
      ],
      'response_strategy' => 'Provide the original transaction record showing the correct authorized amount. Include the evidence bundle which contains the exact amount at time of authorization.',
      'winning_argument'  => 'Transaction amount in evidence bundle matches the authorized amount exactly. No discrepancy exists between authorized and settled amounts.',
    ],
    '13.1' => [
      'code'              => '13.1',
      'network'           => 'visa',
      'description'       => 'Merchandise / Services Not Received',
      'category'          => 'Consumer Dispute',
      'time_limit_days'   => 120,
      'required_evidence' => [
        'delivery_confirmation',
        'transaction_record',
        'ip_address',
        'session_data',
      ],
      'response_strategy' => 'Provide proof of delivery or service fulfillment. Include transaction record showing service was rendered. If digital service, provide IP logs and session data confirming access.',
      'winning_argument'  => 'Evidence bundle confirms transaction was completed and service was accessible from cardholder\'s device and IP address at transaction time.',
    ],
    '13.3' => [
      'code'              => '13.3',
      'network'           => 'visa',
      'description'       => 'Not as Described or Defective Merchandise',
      'category'          => 'Consumer Dispute',
      'time_limit_days'   => 120,
      'required_evidence' => [
        'product_description',
        'transaction_record',
        'communication_logs',
      ],
      'response_strategy' => 'Provide product or service description that matches what was delivered. Include any communication logs with cardholder. Demonstrate that service matched the description at time of purchase.',
      'winning_argument'  => 'Service delivered matched the description presented at checkout. No prior complaints were received from this cardholder.',
    ],
    '13.6' => [
      'code'              => '13.6',
      'network'           => 'visa',
      'description'       => 'Credit Not Processed',
      'category'          => 'Consumer Dispute',
      'time_limit_days'   => 120,
      'required_evidence' => [
        'refund_record',
        'transaction_record',
      ],
      'response_strategy' => 'Provide credit or refund transaction record if applicable. If no refund was issued, provide evidence that the original transaction was valid and refund was not warranted.',
      'winning_argument'  => 'Transaction was valid per evidence bundle. No refund obligation exists as service was fully rendered.',
    ],
  ];

  private array $mastercardCodes = [
    '4853' => [
      'code'              => '4853',
      'network'           => 'mastercard',
      'description'       => 'Cardholder Dispute – Defective/Not as Described',
      'category'          => 'Cardholder Dispute',
      'time_limit_days'   => 120,
      'required_evidence' => [
        'transaction_record',
        'service_description',
        'risk_score',
        'device_fingerprint',
      ],
      'response_strategy' => 'Submit evidence bundle demonstrating the transaction was legitimate, properly authorized, and that the service matched the described offering. Include device and session data.',
      'winning_argument'  => 'Cryptographic evidence bundle confirms transaction legitimacy. Risk assessment at authorization time showed no fraud indicators.',
    ],
    '4855' => [
      'code'              => '4855',
      'network'           => 'mastercard',
      'description'       => 'Goods or Services Not Provided',
      'category'          => 'Cardholder Dispute',
      'time_limit_days'   => 120,
      'required_evidence' => [
        'delivery_proof',
        'transaction_record',
        'session_data',
        'ip_address',
      ],
      'response_strategy' => 'Provide proof that goods or services were delivered. For digital services, include session logs, IP address, and device fingerprint showing cardholder accessed the service.',
      'winning_argument'  => 'Service was provided and accessed from cardholder\'s registered device and IP address. Session data in evidence bundle confirms access.',
    ],
    '4859' => [
      'code'              => '4859',
      'network'           => 'mastercard',
      'description'       => 'Addendum, No-show, or ATM Dispute',
      'category'          => 'Cardholder Dispute',
      'time_limit_days'   => 120,
      'required_evidence' => [
        'transaction_record',
        'authorization_record',
        'risk_score',
      ],
      'response_strategy' => 'Provide complete transaction and authorization records. Demonstrate that the transaction was properly authorized and no anomalies were present at transaction time.',
      'winning_argument'  => 'Transaction was fully authorized with clean risk signals. Evidence bundle provides complete audit trail from authorization to completion.',
    ],
    '4863' => [
      'code'              => '4863',
      'network'           => 'mastercard',
      'description'       => 'Cardholder Does Not Recognize Transaction',
      'category'          => 'Fraud',
      'time_limit_days'   => 120,
      'required_evidence' => [
        'device_fingerprint',
        'ip_address',
        'session_data',
        'velocity_signals',
        'risk_score',
      ],
      'response_strategy' => 'This is the strongest use case for the evidence bundle. Present device fingerprint, IP geolocation, session data, and risk score. Demonstrate that all signals pointed to the legitimate cardholder.',
      'winning_argument'  => 'Transaction originated from a known device with established session history. IP geolocation is consistent with cardholder\'s registered region. Risk score was within safe threshold.',
    ],
    '4834' => [
      'code'              => '4834',
      'network'           => 'mastercard',
      'description'       => 'Duplicate Processing',
      'category'          => 'Processing Error',
      'time_limit_days'   => 90,
      'required_evidence' => [
        'transaction_record',
        'idempotency_proof',
      ],
      'response_strategy' => 'Provide idempotency key evidence proving each transaction was unique. Show that duplicate processing did not occur.',
      'winning_argument'  => 'Each transaction carried a unique idempotency key. Evidence bundle confirms this was a single, unique authorization event.',
    ],
    '4837' => [
      'code'              => '4837',
      'network'           => 'mastercard',
      'description'       => 'No Cardholder Authorization',
      'category'          => 'Fraud',
      'time_limit_days'   => 120,
      'required_evidence' => [
        'device_fingerprint',
        'ip_address',
        'session_age',
        'risk_score',
        'bin_risk',
        'velocity_signals',
      ],
      'response_strategy' => 'Present full risk signal breakdown showing transaction was authorized through legitimate channels. Include device, session, IP, and velocity data.',
      'winning_argument'  => 'Multi-signal risk assessment confirmed authorization legitimacy. No anomalies detected across device, network, session, velocity, or BIN risk signals.',
    ],
    '4840' => [
      'code'              => '4840',
      'network'           => 'mastercard',
      'description'       => 'Fraudulent Processing of Transactions',
      'category'          => 'Fraud',
      'time_limit_days'   => 120,
      'required_evidence' => [
        'risk_score',
        'velocity_signals',
        'device_fingerprint',
        'ip_address',
      ],
      'response_strategy' => 'Submit complete evidence bundle demonstrating legitimate transaction processing. Show that all fraud detection signals were within acceptable thresholds at time of processing.',
      'winning_argument'  => 'Transaction processing followed standard risk assessment protocol. All 6 risk signals were evaluated and composite score was within safe threshold.',
    ],
  ];

  public function find(string $network, string $code): ?array
  {
    $codes = strtolower($network) === 'visa'
      ? $this->visaCodes
      : $this->mastercardCodes;

    return $codes[$code] ?? null;
  }

  public function all(string $network): array
  {
    return strtolower($network) === 'visa'
      ? $this->visaCodes
      : $this->mastercardCodes;
  }

  public function allCodes(): array
  {
    return array_merge($this->visaCodes, $this->mastercardCodes);
  }

  public function getNetworkForCode(string $code): string
  {
    if (isset($this->visaCodes[$code]))       return 'visa';
    if (isset($this->mastercardCodes[$code])) return 'mastercard';
    return 'visa'; // default
  }
}
