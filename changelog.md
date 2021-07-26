# Changelog

#### Legend

- Bug Fix (-)
- Feature Addition (+)
- Improvement (^)

## com_tjvendors v1.4.3

#### - Bug Fixes:
- #171823 Showing JS error in console
- #170524 If TJ-Notifications is disabled then user is unable to create vendor
- #170325 On payout transaction completion the 'Vendor fee successfully saved' message is shown
- #170204 Function "checkGatewayDetails" always returns false as the code is not updated to adapt to the new structure to save payment details in the vendor profile
- #169366 Housekeeping script of version 1.4.2 gives error for site where TJ-Notifications is not installed

##### ^ Improvements:
- #171108 Added various plugin triggers in tjvendors
- #170205 Function addEntry should take 'currency' from the function params and if it's not sent then it should try to get the 'currency from the client extension configuration
- #169696 Make com_tjvendors php8 compatible
- #165603 Added getcountry, city, region methods in vendor class
- #136504 Quick2Cart integration changes


## com_tjvendors v1.4.2

#### + Features Added:
- #165665 TJVendors integration with latest TJNotifications (Added SMS support)

##### ^ Improvements:
- #164184 Backend Vendor fees list view added action on checkboxes for edit vendor
