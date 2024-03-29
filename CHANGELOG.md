#### 2.4.0

- Drop semicolon from Response errorMessages()
- Fix PHP 8 warnings
- Update dependencies

#### 2.3.0

- Update dependencies

#### 2.2.0

- Add locale support
- Use system's CA certificate store instead

#### 2.1.0

- Enable auto-loading JWT keys from LoginRocket

#### 2.0.0

NOTE: This version is only compatible with AuthRocket 2. Use package version 1.x with AuthRocket 1.

- Update for AuthRocket 2 API
- Rename ENV AUTHROCKET_JWT_SECRET -> AUTHROCKET_JWT_KEY and jwtSecret -> jwtKey
- Add Invitation
- Update Session, User

#### 1.3.0

- Use system's CA certificate store instead
- Fix parsing error

#### 1.2.1

- Update certs

#### 1.2.0

- add Credential
- Add User\authenticateCode()

#### 1.1.0

- Fix error on PHP 7
- Support RS256 signed tokens

#### 1.0.1

- Fix variable scope in Session\fromToken()

#### 1.0.0

- AuthRocket/php is all grown up! (aka v1.0.0)
- Parse custom attributes from JWT when available

#### 0.9.1

- Eliminate several PHP Notices

#### 0.9.0

- Initial release
