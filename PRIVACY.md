# Charitable & User Privacy
Charitable stores personal data about donors and registered users in **your website's database**, as well as providing personal data to third party providers such as payment gateways (PayPal, Stripe, Authorize.Net, etc.) or newsletter providers (MailChimp, Campaign Monitor, MailerLite, etc.).

All or some of the following data may be collected and stored about a donor when they make a donation:

- First & last name
- Email address
- Street address (street number & name, city, post code/zip code, state/province, country)
- Phone number
- Credit/debit card tokens

All or some of the above data may be shared with the payment gateway used to process the donation. If using [Newsletter Connect](https://www.wpcharitable.com/extensions/charitable-newsletter-connect/), the donor's personal data may also be shared with your chosen newsletter provider.

For credit card donations processed by Stripe or Authorize.Net, the following data is provided to the chosen payment gateway. **NOTE: These details are not stored in your database**:

- Credit/debit card number
- Expiry date
- CVS

In addition, the following data may be collected for registered users when they create a fundraising campaign (requires [Ambassadors]((https://www.wpcharitable.com/extensions/charitable-ambassadors/)) or when they fill out their personal profile:

- First & last name
- Email address
- Street address (street number & name, city, post code/zip code, state/province, country)
- Phone number
- Organisation
- Bio / personal description
- Website URL
- Facebook profile URL
- Twitter profile URL

## Exporting Personal Data
As of WordPress 4.9.6, WordPress includes a personal data export tool which can be used to provide users with a downloadable file that shows all personal data your website contains about them. This data export will include the personal data mentioned above.

The personal data export tool can be reached via _Tools_ > _Export Personal Data_.

## Erasing Personal Data
As of WordPress 4.9.6, WordPress includes a personal data removal tool which can be used to erase the personal data stored by your website about a particular user. Your users may request to have their data removed; as of May 2018, this is a right of European residents and you are obliged to comply.

The personal data removal tool can be reached via _Tools_ > _Remove Personal Data_.

You may be required to keep personal data relating to donations for a certain amount of time for legal or tax purposes. To support this requirement, you can configure Charitable to prevent personal donation data from being erased for donations within a certain time limit (i.e. the last three years). You can also mark which pieces of data must be retained for donations made within this time. 

These settings can be configured via _Charitable_ > _Settings_ > _Advanced_.

## Privacy Policy
As of WordPress 4.9.6, WordPress includes a tool that can help build your website Privacy Policy page. This will include suggested privacy policy text added by the plugins on your website, including Charitable.

You can access this tool via _Tools_ > _Privacy_. You are strongly advised to carefully review the suggested privacy policy text added by your plugins and tailor it to your website needs.
