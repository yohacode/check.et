# Configuration

```php
$config = new Config(
    apiKey: getenv('CHECK_ET_API_KEY'),
    timeout: 30,
    retries: 3
);
```

## Options

| Option | Default |
|---------|---------|
| timeout | 30 |
| connectTimeout | 5 |
| retries | 3 |

## Security

- Always store API keys in environment variables.
- Never commit API keys to source control.
- HTTPS is mandatory.
