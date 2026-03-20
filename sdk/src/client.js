export class ChargebackShieldError extends Error {
    constructor(message, status, errors = {}) {
        super(message);
        this.name = "ChargebackShieldError";
        this.status = status;
        this.errors = errors;
    }
}

export class AuthenticationError extends ChargebackShieldError {
    constructor(message = "Invalid or missing API key.") {
        super(message, 401);
        this.name = "AuthenticationError";
    }
}

export class ValidationError extends ChargebackShieldError {
    constructor(message, errors) {
        super(message, 422, errors);
        this.name = "ValidationError";
    }
}

export class RateLimitError extends ChargebackShieldError {
    constructor(retryAfter) {
        super("Rate limit exceeded. Slow down and try again.", 429);
        this.name = "RateLimitError";
        this.retryAfter = retryAfter;
    }
}

export class NotFoundError extends ChargebackShieldError {
    constructor(message = "Resource not found.") {
        super(message, 404);
        this.name = "NotFoundError";
    }
}
