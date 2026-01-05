# Javascript Callbacks
Woo QuiBuy provides a Javascript event that triggers when the quantity or price is updated in the modal.

## Event Name: `woo_quibuy_price_updated`

### Description
This event is dispatched on the `document` object whenever the quantity input changes within the Quick Buy modal.

### Event Detail
The `detail` property of the event object contains the following information:

| Property | Type | Description |
| :--- | :--- | :--- |
| `quantity` | `number` | The current quantity selected. |
| `total` | `number` | The calculated total price (Quantity * Unit Price). |
| `price` | `number` | The unit price of the product. |
| `formattedTotal` | `string` | The total price formatted as currency (VND). |

### Example Usage

You can listen for this event to perform custom actions, such as updating other UI elements or logging data.

```javascript
document.addEventListener('woo_quibuy_price_updated', function(e) {
    console.log('Price Updated!');
    console.log('Quantity:', e.detail.quantity);
    console.log('Total:', e.detail.total);
    console.log('Formatted Total:', e.detail.formattedTotal);
    
    // Example: Update an external element with the new total
    // document.getElementById('my-custom-total').innerText = e.detail.formattedTotal;
});
```
