# CiviCRM: Referral Source

CiviCRM extension providing custom behaviors for Contributions:

*  Allows modifying the contribution Source field based on the 'source' query string parameter, if such is provided in the URL for a contribution page.

The extension is licensed under [GPL-3.0](LICENSE.txt).

## Requirements

* PHP v7.0+
* CiviCRM 4.7

## Usage

For any contribution page, append `&source=X` to the URL. This will cause 'X' to be appended to the resulting contribution Source field value.

## Credits
Developed by [Joinery](https://joineryhq.com). Concept and initial sponsorship by [Korlon](https://korlon.com).

## Support

Support for this package is handled under Joinery's ["Limited Support" policy](https://joineryhq.com/software-support-levels#limited-support).

Public issue queue for this package: [https://github.com/twomice/com.joineryhq.referralsource/issues](https://github.com/twomice/com.joineryhq.referralsource/issues)
