# Changelog

Notable changes to `crmleaf/tds-calculator`.

Format per [Keep a Changelog](https://keepachangelog.com/en/1.1.0/); versioning
per [Semantic Versioning](https://semver.org/spec/v2.0.0.html) - with one extra
rule this package observes, because it computes statutory figures:

> **Any change that alters a published result is at minimum a minor release**,
> and is listed under `Changed` with the notification, circular or Act section
> that prompted it.

## [Unreleased]

## [1.0.0] - 2026-08-12

### Added

- Initial release. Works out the tax to deduct from this month's salary by annualising the pay, taxing it properly, subtracting what has already been deducted, and spreading the balance over the months that remain.

### Statutory basis

- Income-tax Act 1961, section 192 read with section 115BAC, on the FY 2025-26 slabs given by the Finance Act 2025, including the section 87A rebate with its marginal relief and section 288B rounding.

[Unreleased]: https://github.com/crmleaf/tds-calculator/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/crmleaf/tds-calculator/releases/tag/v1.0.0
