# Email validator

## Purpose

Check:

* is email valid (simple regexp)
* does email exist on server

## Usage

```php
$validator = new \EmailValidator\EmailValidator('test121212321312@gmail.com', 'mydomain.com', 'noreply@mydomain.com');

if ($validator->validate()) {

    echo 'valid email';

} else {

    echo 'invalid email';

}
```

## Important

Only for non-IDN emails