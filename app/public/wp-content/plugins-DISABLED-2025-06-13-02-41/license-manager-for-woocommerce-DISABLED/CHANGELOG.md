##### 3.0.12 - 2025-04-15
- Tweak - Tested Upto WordPress Latest Version 6.8

##### 3.0.11 - 2025-01-02
- Improved - Change menus position under Products

##### 3.0.10 - 2024-12-05
- Improved - Minor bug fixes and improvements

##### 3.0.9 - 2024-11-12
- Improved - Minor bug fixes and improvements

##### 3.0.8 - 2024-07-22
- Improved - Performance improvements and bug fixes
- Improved - Enhanced stability and reliability##### 3.0.7 - 2024-05-16
- Improved - License page error handling if no license found.
- Improved - API filter parameters for ammending data.

##### 3.0.6 - 2024-03-12
- Improved - Scripts would load on license manager specific pages only.

##### 3.0.5 - 2023-12-05
- Fixed - Settings not updating after update.
- Fixed - Php warning in some cases.

##### 3.0.4 - 2023-11-24
- Fixed - License key was not appearing on My Account page.
- Fixed - Php notices in some cases.
- Fixed - Code optimization.
- Fixed - License keys not receiving when order is processing.
- Fixed - PDF not downloading until the order is not completed.

##### 3.0.3 - 2023-11-18
- Fixed - Php warnings appears in some cases.

##### 3.0.2 - 2023-11-15
- Fixed - License not activating via API

##### 3.0.1 - 2023-11-15
- Fixed - Through Php errors in some cases

##### 3.0 - 2023-11-14
Added - License Activations
Added - License and Generator delete endpoints
Added - License PDF Certificates 
Added - Migration and Past Order License Generator tools
Added - License Expiration Format
Added - Single License Page in My account
Fixed - UserId variable in lmfwc_add_license function
Fixed - OrderBy query Vulnerability 

##### 2.2.11 - 2023-09-13
* Fix - OrderBy Query Vulnerability 

##### 2.2.10 - 2023-08-01
* Fix - The reported vulnerability has been resolved by updating the Feedback SDK to the latest version.

##### 2.2.9 - 2023-06-28
* Tested up to WooCommerce v7.8.0 and WordPress v6.2.2

##### 2.2.8 - 2022-08-23
* Update - Upgrade Menu Added

##### 2.2.7 - 2022-04-26
* Update - Changed main menu structure.
* Update - Moved License Keys inside WooCommerce menu
* Update - Moved Generators inside WooCommerce menu
* Update - Moved Settings inside WooCommerce-> Settings -> License Manager

##### 2.2.5 - 2021-10-21
* Update - Freemius Integrated
* Update - PHP 7.0 compatibility

##### 2.2.4 - 2021-07-26
* Update - WordPress 5.8 compatibility
* Update - WooCommerce 5.5 compatibility

##### 2.2.3 - 2021-06-08
* Update - WordPress 5.7 compatibility
* Update - WooCommerce 5.4 compatibility

##### 2.2.2 - 2021-02-19
* Update - WordPress 5.6 compatibility
* Update - WooCommerce 5.0 compatibility
* Fix - The "Licenses" page no longer causes a blank page or PHP memory_limit error when a large amount of orders and licenses is present in the database.

##### 2.2.1 - 2020-10-03
* Update - WordPress 5.5 compatibility
* Update - WooCommerce 4.5 compatibility
* Fix - License user ID is no longer being overwritten with the user ID of the currently logged in administrator when manually completing an order in the backend.
* Fix - The plugin no longer throws a PHP Error when visiting "My Account" if there are licenses assigned to deleted WooCommerce products.
* Fix - `register_rest_route()` no longer throws a PHP notice.
* Fix - The plugin now prevents license activation/deactivation if the license key has expired.

##### 2.2.0 - 2020-04-10
* Add - Functions for license operations: `lmfwc_add_license()`, `lmfwc_get_license()`, `lmfwc_update_license()`, `lmfwc_delete_license()`, `lmfwc_activate_license()`, and `lmfwc_deactivate_license()`
* Add - Maximum activation count (`times_activated_max`) now allows for unlimited activations if the value is left empty (`null`)
* Add - It is now possible to select on which order status changes licenses will be generated ("Completed", "Processing", etc.)
* Add - Customers can now activate and deactivate their license keys inside "My Account" if the setting is enabled.
* Add - The "allow duplicate license keys" setting has been added.
* Add - STOPPED AT MERGE PULL REQUEST #740
* Add - A "User ID" field has been added on the license key level. Add/Import forms and REST route have been updated to allow for this new parameter.
* Add - User ID automatically gets assigned to a license key when a customer purchases said license key.
* Add - Automatic stock management. License key stock will now automatically be adjusted when adding, deleting, and selling license keys. Can be turned off via the settings.
* Add - The License table columns can now be expanded via the following filters:  `lmfwc_table_licenses_column_name`, `lmfwc_table_licenses_column_value`, and `lmfwc_table_licenses_column_sortable`
* Add - The CSV export can now be customized via the settings.
* Add - The CSV export can also be customized with the following filter: `lmfwc_export_license_csv`.
* Add - Permissions to REST API routes. Currently, all REST API routes require the `manage_options` permission for both objects (licenses and generators). Can be customized with the following filter: `lmfwc_rest_check_permissions`
* Fix - the `lmfwc_rest_api_validation` filter has been fixed.
* Fix - The plugin will no longer throw PHP errors or notices on the "Licenses" page inside "My Account" when a product is missing.
* Fix - Fix the Show/Hide/Copy buttons for variable products and other scenarios.
* Fix - On the "Licenses" page, the order filter dropdown now displays the order sorted by the order ID, in a descending manner.
* Fix - When selling existing license keys, the "Expires at" field will be preserved after purchase.
* Fix - Product data is now being properly saved for variable products.
* Fix - The text domain is now properly set to `license-manager-for-woocommerce`. Thanks to @sebastienserre for pointing this out and fixing it!
* Tweak - Removed the legacy V1 API routes.
* Tweak - Updated the database tables structure.
* Tweak - Searchable dropdown fields (select2) added to the license page filters.
* Tweak - The admin notices class has been reworked and now supports multiple notices.
* Tweak - Refactored the abstract resource repository.

##### 2.1.2 - 2019-12-09
* Add - The plugin now checks the PHP version upon activation. If the version is on/below 5.3.29, the plugin will not activate.
* Add - `lmfwc_event_post_order_license_keys` event action has been added. You can hook-in with the `add_action()` function.
* Fix - Removed the "public" properties from the class constants.
* Fix - Column screen options now work for the license and generator pages.
* Fix - Timestamps are now properly converted and displayed on the licenses page.

##### 2.1.1 - 2019-11-19
* Fix - Adding a generator without a "expires_at" no longer display the "-0001-11-30" date value. You will need to edit existing license keys, remove the value and save them to get rid of the invalid date.
* Fix - If no generators are present, the plugin would throw a PHP notice when going to the "Generate" page inside on the "Generators" menu page.
* Tweak - It is now possible to create API keys without WooCommerce installed.
* Tweak - Removed the redundant plugin Exception class.

##### 2.1.0 - 2019-11-13
* Update - WordPress 5.3 compatibility
* Update - WooCommerce 3.8 compatibility
* Add - Introduced a License key meta table, along with add/update/get/delete functions.
* Add - The plugin now checks for duplicates before adding or editing license keys (this also applies to the API).
* Add - Generators can now freely generate license keys and add them directly to the database.
* Add - `lmfwc_rest_api_validation` filter for additional authentication or data validation when using the REST API.
* Add - Field for copy-pasting license keys on the "Import" page.
* Add - "Mark as sold" and "Mark as delivered" bulk actions on the license keys page.
* Add - A new "My license keys" section for customers, under the "My account" page.
* Add - The "Expires at" field can now directly be edited when adding or editing license keys. This also applies to the API.
* Tweak - Code reformat, refactor, and cleanup.
* Fix - Typo on the Settings page (the `v2/licenses/activate/{license-key}` route now displays correctly as a GET route).
* Fix - The `activate` and `deactivate` license key actions now work on the license keys overview.
* Fix - When adding or editing license keys, the "Product" field now also searches product variations.
* Fix - Multiple admin notices can now be displayed at once.
* Fix - Automatic loading of plugin translations.

##### 2.0.1 - 2019-09-03
* Add - v2/deactivate/{license_key} route for license key deactivation.
* Add - "Clear" functionality to order and product select2 dropdown menus.
* Fix - License key status dropdown order ("Active" is first now).
* Fix - PHP fatal error when deleting license keys.
* Fix - PHP Notices when performing certain operations (license key import, generator delete).
* Fix - "lmfwc_rest_api_pre_response" hook priority is now correctly set to 1.

##### 2.0.0 - 2019-08-30
* Add - Template override support.
* Add - Select2 dropdown fields for orders and products when adding or editing license keys.
* Add - Search box for license keys. Only accepts the complete license keys, will not find parts of it.
* Add - v2 API routes
* Add - Setting for enabling/disabling specific API routes.
* Add - `lmfwc_rest_api_pre_response` filter, which allows to edit API responses before they are sent out.
* Tweak - Complete code rework.
* Tweak - Reworked v1 API routes (maintaining compatibility)
* Fix - Users can now edit and delete all license keys, even sold/delivered ones.
* Fix - WordPress installations with large numbers of orders/products could not open the add/edit license key page.
* Fix - CSS fallback font for the license key table.
* Fix - "Valid for" text in customer emails/my account no longer shows if the field was empty.

##### 1.2.3 - 2019-04-21
* Add - Filter to change the "Valid until" text inside the emails (`lmfwc_license_keys_table_valid_until`).
* Fix - Minor CSS fixes.
* Fix - When selling license keys, the "Expires at" field would be set even when not applicable. This does not happen anymore.

##### 1.2.2 - 2019-04-19
* Add - German plugin translation

##### 1.2.1 - 2019-04-18
* Fix - "There was a problem adding the license key." error message should not appear any more when adding a license key.

##### 1.2.0 - 2019-04-17
* Add - You can now define how many times a license key can be activated using the plugin REST API endpoints.
* Add - You can now define how many license keys will be delivered on purchase.
* Add - Variable product support.
* Add - Export license keys feature (CSV/PDF)
* Add - License key activation REST API endpoint.
* Add - License key validation REST API endpoint.
* Add - New WooCommerce Order action to manually send out license keys.
* Add - "Expires on" date to Customer order emails and Customer order page.
* Add - Filter to replace the "Your License Key(s)" text in the customer email and "My account" page (`lmfwc_license_keys_table_heading`).
* Add - Generators now display the number of products to which they are assigned next to their name.
* Enhancement - Various UI improvements across the plugin.
* Tweak - The "Add/Import" button and page have been renamed to "Add license"
* Tweak - The GET license/{id} REST API endpoint now supports the license key as input parameter as well.
* Tweak - Changes to the REST API response structure.
* Tweak - Changes to the database structure.
* Fix - The license key product settings will no longer be lost when using quick edit on products.

##### 1.1.4 - 2019-03-30
* Fix - Licenses keys will no longer be sent out more than once if you change the order status from "complete" to something else and then back to "complete".

##### 1.1.3 - 2019-03-24
* Fix - On some environments the activate hook wouldn't work properly and the needed cryptographic secrets weren't generated. I negotiated a deal for this not to happen anymore.
* Fix - When going to the REST API settings page you no longer get a 500 error. Once again, my mistake.
* Fix - Removed unused JavaScript code. It was just lurking there for no purpose, at all.

##### 1.1.2 - 2019-03-24
* Feature - Clicking license keys inside the table now copies them into your clipboard. Cool huh?
* Fix - CSV and TXT upload of license keys now works as expected again. I hope.
* Tweak - Minor UI improvements on the licenses page. I made stuff look cool(er).

##### 1.1.1 - 2019-03-23
* Fix - The cryptographic secrets were being deleted on plugin update, causing the plugin to become unusable after the 1.1.0 update. I'm really sorry for this one.

##### 1.1.0 - 2019-03-23
* Feature - Added license and generator api routes. Currently available calls are GET (single/all), POST (create), and PUT (update) for both resources.
* Feature - API Authentication for the new routes. Currently only basic authentication over SSL is supported.
* Feature - Editing license keys is now possible.
* Feature - Added a "valid for" field on the bulk import of license keys.
* Tweak - The plugin now supports license key sizes of up to 255 characters.
* Tweak - Major code restructuring. Laid the foundation for future features.
* Tweak - Reworked the whole plugin to make use of filters and actions.
* Enhancement - Minor visual upgrades across the plugin.

##### 1.0.1 - 2019-02-24
* Update - WordPress 5.1 compatibility.
* Update - readme.txt

##### 1.0.0 - 2019-02-19
* Initial release.