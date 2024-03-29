# Module Name: PopulateCatalog

## Description
PopulateCatalog is a Magento 2 module designed to automate the process of populating and updating product and category data in a Magento store. It retrieves data from an external API, manages product and category creation/updating, and sends daily reports about the import process via email.

## Features
- Retrieves product and category data from an external API.
- Automated creation and updating of products and categories.
- Daily execution of the import process.
- Daily reports on import statistics sent via email.
- Admin configuration for report recipient management.

## Prerequisites
- Ensure that your Magento 2 environment has an SMTP server configured for outgoing emails. This module relies on email functionality to send daily reports.

## Installation
1. **Download the Module**
    - Via Composer: Run `composer require juliobarbosa/bombardier-populate-catalog`.
    - Manually: Download and place it into `app/code/JulioBarbosa/BombardierPopulateCatalog`.

2. **Enable the Module**
    - Run `php bin/magento setup:upgrade`.
    - Run `php bin/magento setup:di:compile`.
    - Run `php bin/magento cache:clean`.

3. **Configure the Module**
    - Go to `Stores > Configuration > Bombardier > Import Catalog` in Magento Admin.

## Configuration
- **Email Settings**: Configure email recipients for daily reports under `Stores > Configuration > Bombardier > Import Catalog`.

## Usage
The module runs automatically based on the configured schedule. You can monitor the import process and check email reports for updates.

## Email Report
Daily reports are sent to configured email addresses with details about the products and categories imported. The report includes counts of newly created, updated, and deleted items (if applicable).

## Customization
The module can be customized to suit specific requirements, such as changing the API source or modifying the report format.

## Troubleshooting
- **Check Logs**: Review logs in `var/log` for errors or issues.
- **Cron Jobs**: Ensure cron jobs are set up correctly for scheduled tasks.

## Support
For support, please contact [julio.barbosa.15@gmail.com].
