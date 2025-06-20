# WhatsApp Stock Notification

This feature sends WhatsApp notifications with stock information and recommendations at midnight after the end of active shifts for the day.

## How it Works

1. The system collects stock data from all active shifts of the day.
2. At midnight (00:00), it compiles information about remaining stocks.
3. For ingredients with stock levels below the minimum threshold, it calculates and suggests recommended quantities to order.
4. This information is sent via WhatsApp to each warehouse that has an active WhatsApp configuration.

## Important Notes

- The system **only processes shifts that are not closed** (is_closed = false) for that specific day
- If no active shifts exist for a particular warehouse, no report will be sent for that warehouse

## Requirements

- Warehouse must have WhatsApp number configured and active (`is_whatsapp_active = 1`)
- WhatsApp API service must be properly configured in the system
- Laravel scheduler must be running

## Setup

The scheduler is already configured to run the command at midnight. Make sure your server has a cron job set up to run the Laravel scheduler:

```
* * * * * cd /path/to/sbpos && php artisan schedule:run >> /dev/null 2>&1
```

## Testing

To test the WhatsApp stock notification feature manually, run:

```
php artisan stock:test-whatsapp-notification
```

## Troubleshooting

If WhatsApp messages are not being sent:

1. Check if the warehouse has a valid WhatsApp number set and `is_whatsapp_active = 1`
2. Verify WhatsApp service configuration in the admin panel
3. Check server logs for any errors
4. Try sending a test message through the WhatsApp configuration page

## Notes

- The system uses the `minimum_stock` field in the ingredients table to determine when to recommend restocking
- If an ingredient doesn't have a minimum stock set, the system will use a calculated value based on average usage patterns
