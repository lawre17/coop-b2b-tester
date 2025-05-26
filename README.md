# Co-op Bank B2B Simulator

This Laravel app simulates the Co-operative Bank B2B engine for local testing. It mimics how the bank sends **validation** and **payment advice (IPN)** requests to an institution's back office system, allowing full-cycle integration testing.

---

## 🚀 Features

-   Simulates `POST /validate` request to validate an account (e.g., student number)
-   Simulates `POST /ipn` (payment advice) once validation succeeds
-   Sends scheduled transactions via Laravel's scheduler
-   Can be used to test Co-op's REST-based B2B integration with your main app

---

## ⚙️ Requirements

-   PHP 8.1+
-   Laravel 10+
-   Internet or localhost connection to your back office API (mock or real)

---

## 📦 Installation

```bash
git clone https://github.com/your-org/coop-b2b-simulator.git
cd coop-b2b-simulator
composer install
cp .env.example .env
php artisan key:generate
```

Update `.env` to point to your backend routes:

```env
VALIDATION_TARGET_URL=http://your-backend.test/api/{tenantID}/coop/validation
IPN_TARGET_URL=http://your-backend.test/api/{tenantID}/coop/confirmation
```

Replace `{tenantID}` with an actual tenant ID like `001` in a case of multi-tenant application.

> **Note:** If your backend is **not multi-tenant**, you can omit `{tenantID}` from the URLs. For example:
>
> ```env
> VALIDATION_TARGET_URL=http://your-backend.test/api/coop/validation
> IPN_TARGET_URL=http://your-backend.test/api/coop/confirmation
> ```

---

## 🛠️ How It Works

1. **Simulator triggers a validation request** to your backend’s `/coop/validation` endpoint.
2. If the validation is successful (`statusCode: 200`), it sends a payment advice to `/coop/confirmation`.
3. Your backend is expected to:
    - Validate the identifier (e.g., admission number)
    - Save or process the IPN
    - Respond with the required structure

---

## 🧪 Usage

### Simulate a transaction

```bash
php artisan simulate:bank-b2b
```

This command:

-   Sends a validation request with a fixed or random `TransactionReferenceCode`
-   If validation is successful, sends a payment advice with a dummy amount

### Schedule automatic simulation

Add this to `app/Console/Kernel.php`:

```php
$schedule->command('simulate:bank-b2b')->everyMinute();
```

Run the scheduler:

```bash
php artisan schedule:work
```

---

## 📄 API Format

### Validation Request

```json
POST {{VALIDATION_TARGET_URL}}

{
  "header": {
    "serviceName": "EldoretUniversity",
    "messageID": "uuid",
    "connectionID": "UOE",
    "connectionPassword": "8786%$"
  },
  "request": {
    "TransactionReferenceCode": "EDA/1140/13",
    "TransactionDate": "2025-05-25T22:00:00+03:00",
    "InstitutionCode": "2100082"
  }
}
```

### Expected Validation Response

```json
{
    "header": {
        "messageID": "uuid",
        "statusCode": "200",
        "statusDescription": "Successfully validated student"
    },
    "response": {
        "TransactionReferenceCode": "EDA/1140/13",
        "TransactionDate": "2025-05-25T22:00:00+03:00",
        "TotalAmount": 0.0,
        "Currency": "",
        "AdditionalInfo": "John Doe",
        "AccountNumber": "EDA/1140/13",
        "AccountName": "John Doe",
        "InstitutionCode": "2100082",
        "InstitutionName": "Eldoret University"
    }
}
```

### Payment Advice (IPN) Format

```json
POST {{IPN_TARGET_URL}}

{
  "header": {
    "serviceName": "EldoretUniversity",
    "messageID": "uuid",
    "connectionID": "UOE",
    "connectionPassword": "8786%$"
  },
  "request": {
    "TransactionReferenceCode": "TXN998877",
    "TransactionDate": "2025-05-25T22:00:00+03:00",
    "TotalAmount": 100,
    "Currency": "",
    "DocumentReferenceNumber": "EDA/1140/13",
    "BankCode": "00011",
    "BranchCode": "00011001",
    "PaymentDate": "2025-05-25T22:00:00+03:00",
    "PaymentReferenceCode": "",
    "PaymentCode": "",
    "PaymentMode": "1",
    "PaymentAmount": 100,
    "AdditionalInfo": "EDA/1140/13",
    "AccountNumber": "EDA/1140/13",
    "AccountName": "John Doe",
    "InstitutionCode": "2100082",
    "InstitutionName": "Eldoret University"
  }
}
```

---

## 📂 File Structure

-   `app/Console/Commands/SimulateBankTransaction.php`: Laravel command to simulate transactions
-   `app/Services/CoopSimulatorService.php`: Handles sending validation and IPN payloads
-   `.env`: Contains target backend URLs
-   `routes/console.php`: Contains CLI-only commands
-   `schedule:work`: Laravel task scheduler to automate simulation

---

## 👨‍💻 Author

Built by Lawrence Njoroge, for local Co-op B2B API testing and simulation.

---

## 📄 License

MIT — feel free to use, modify, or contribute.
