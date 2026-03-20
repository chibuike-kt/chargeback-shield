# chargeback-shield

Official Node.js SDK for [Chargeback Shield](https://chargebackshield.io) —
real-time chargeback protection for African fintechs.

## Installation
```bash
npm install chargeback-shield
```

## Quick start
```js
import ChargebackShield from 'chargeback-shield';

const shield = new ChargebackShield('cs_live_your_key_here', {
  webhookSecret: 'whsec_your_secret_here',
});
```

## Post-auth scoring (recommended for volume)

Call after approving — fire and forget. Zero latency added.
```js
async function processTransaction(txData) {
  // Approve with your existing flow
  const approval = await cardNetwork.authorize(txData);

  // Score in background — don't await
  shield.transactions.score({
    idempotencyKey:    txData.id,
    cardBin:           txData.card.bin,
    cardLast4:         txData.card.last4,
    cardCountry:       txData.card.country,
    amount:            txData.amount,
    currency:          txData.currency,
    ipAddress:         txData.ip,
    ipCountry:         txData.geoCountry,
    deviceFingerprint: txData.device.fingerprint,
    sessionAgeSeconds: txData.session.age,
    merchantCategory:  txData.mcc,
  }).catch(console.error);

  return approval;
}
```

## Pre-auth interception (high-value transactions)
```js
const result = await shield.transactions.intercept({
  idempotencyKey: 'order_12345',
  cardBin:        '459234',
  cardLast4:      '4242',
  cardCountry:    'NG',
  amount:         5000000,
  currency:       'NGN',
  ipCountry:      'NG',
});

if (result.decision === 'decline') throw new Error('Transaction declined');
if (result.decision === 'step_up') return trigger3DS(result.transactionId);
```

## File a dispute
```js
const dispute = await shield.disputes.file({
  transactionId: 'transaction_ulid',
  reasonCode:    '4863',
  network:       'mastercard',
});

console.log(dispute.response_document); // Ready to submit
```

## Verify webhooks
```js
app.post('/webhooks/chargeback-shield', (req, res) => {
  const sig = req.headers['x-chargeback-shield-sig'];

  let event;
  try {
    event = shield.webhooks.constructEvent(req.body, sig);
  } catch (err) {
    return res.status(401).send('Invalid signature');
  }

  if (event.event === 'transaction.high_risk_detected') {
    await flagAccountForReview(event.transaction_id);
  }

  res.json({ received: true });
});
```

## Error handling
```js
import { ValidationError, RateLimitError, AuthenticationError } from 'chargeback-shield';

try {
  const result = await shield.transactions.intercept(params);
} catch (err) {
  if (err instanceof ValidationError) {
    console.error('Validation failed:', err.errors);
  } else if (err instanceof RateLimitError) {
    console.error('Rate limited. Retry after:', err.retryAfter, 'seconds');
  } else if (err instanceof AuthenticationError) {
    console.error('Bad API key');
  } else {
    console.error('Unexpected error:', err.message);
  }
}
```

## License

MIT
