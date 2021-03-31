# Changelog

#### Legend

- Bug Fix (-)
- Feature Addition (+)
- Improvement (^)

## com_tjvendors v1.4.3

#### - Bug Fixes:
- #169366 Housekeeping script of version 1.4.2 gives error for site where TJ-Notifications is not installed
- #170204 Function "checkGatewayDetails" always returns false as the code is not updated to adapt to the new structure to save payment details in the vendor profile

##### ^ Improvements:
- #170205 Function addEntry should take 'currency' from the function params and if it's not sent then it should try to get the 'currency from the client extension configuration

---

## com_tjvendors v1.4.2

#### + Features Added:
- #165665 TJVendors integration with latest TJNotifications (Added SMS support)

##### ^ Improvements:
- #164184 Backend Vendor fees list view added action on checkboxes for edit vendor
