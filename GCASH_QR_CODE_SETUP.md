# GCash QR Code Setup Instructions

## 📸 How to Add Your GCash QR Code

### Option 1: Using GCash Merchant QR Code

1. **Get Your QR Code from GCash**
   - Log in to GCash merchant portal
   - Navigate to "Payment Options" or "QR Code"
   - Download your static merchant QR code
   - Save as PNG or JPG format

2. **Upload to Laravel Storage**
   ```bash
   # Windows PowerShell
   Copy-Item "C:\Path\To\Your\gcash_qr.png" "storage\app\public\gcash_qr.png"
   
   # Create symbolic link if not exists
   php artisan storage:link
   ```

3. **Verify Upload**
   - Check file exists: `storage/app/public/gcash_qr.png`
   - Test access: `http://yourdomain.com/storage/gcash_qr.png`

### Option 2: Generate QR Code for Testing

If you don't have a GCash merchant account yet, you can:

1. **Use a QR Generator**
   - Visit: https://www.qr-code-generator.com/
   - Select "Text" type
   - Enter your GCash mobile number: `09XXXXXXXXX`
   - Download the QR code

2. **Or Create Placeholder**
   - Use any 256x256px image temporarily
   - Name it `gcash_qr.png`
   - Place in `storage/app/public/`

## 🖼️ QR Code Specifications

| Property | Value |
|----------|-------|
| **File Format** | PNG (recommended) or JPG |
| **Dimensions** | 256x256 pixels (minimum) |
| **File Size** | Under 500KB |
| **Filename** | `gcash_qr.png` (exact match) |
| **Location** | `storage/app/public/gcash_qr.png` |
| **Permissions** | Readable by web server |

## 🔄 Update QR Code Path (Optional)

If you want to use a different filename or location:

**Edit:** `resources/views/pos/create.blade.php`

**Find (around line 570):**
```blade
<img src="{{ asset('storage/gcash_qr.png') }}" 
     alt="GCash QR Code" 
     class="w-64 h-64 object-contain">
```

**Change to:**
```blade
<img src="{{ asset('storage/your_custom_name.png') }}" 
     alt="GCash QR Code" 
     class="w-64 h-64 object-contain">
```

## 🧪 Test Display

1. **Navigate to POS**: `http://yourdomain.com/pos/create`
2. **Add items to cart**
3. **Select "GCash" payment method**
4. **Click "Show GCash QR Code"**
5. **Verify QR image displays correctly**

## 🎨 Customize QR Display Size

Edit the modal in `resources/views/pos/create.blade.php`:

```blade
<!-- Change w-64 h-64 to your preferred size -->
<img src="{{ asset('storage/gcash_qr.png') }}" 
     alt="GCash QR Code" 
     class="w-80 h-80 object-contain"> <!-- Larger: 320x320px -->
```

**Size Options:**
- `w-48 h-48` = 192x192px
- `w-64 h-64` = 256x256px (default)
- `w-80 h-80` = 320x320px
- `w-96 h-96` = 384x384px

## 🔒 Security Notes

- **Static QR**: Shows same QR to all customers
- **Manual Verification**: Cashier confirms payment via reference number
- **No API**: This implementation doesn't connect to GCash API
- **Trust-based**: Assumes customer provides valid reference

## 📱 GCash App Reference Number

The reference number customers need to provide is typically:
- **Length**: 13-15 characters
- **Format**: Numeric or alphanumeric
- **Location in App**: Transaction details after payment
- **Example**: `1234567890123`

## ✅ Verification Checklist

- [ ] QR code file uploaded
- [ ] File named exactly `gcash_qr.png`
- [ ] Located in `storage/app/public/`
- [ ] Storage link created (`php artisan storage:link`)
- [ ] QR displays in modal
- [ ] QR is scannable with GCash app
- [ ] Payment completes successfully in test

## 🆘 Troubleshooting

### QR Not Displaying
**Symptom**: Gray placeholder shows instead of QR

**Solutions**:
1. Check file path: `storage/app/public/gcash_qr.png`
2. Verify symbolic link: `public/storage` → `storage/app/public`
3. Run: `php artisan storage:link`
4. Check file permissions (755 recommended)
5. Clear browser cache

### QR Not Scannable
**Symptom**: GCash app can't read QR

**Solutions**:
1. Ensure QR is actual GCash merchant QR
2. Check image quality (not pixelated)
3. Verify QR resolution (at least 256x256)
4. Test with different GCash accounts
5. Regenerate QR from GCash portal

### Wrong QR Displays
**Symptom**: Different image shows

**Solutions**:
1. Check filename spelling (case-sensitive on Linux)
2. Clear Laravel cache: `php artisan cache:clear`
3. Restart web server
4. Verify asset path in blade template

## 📞 Support

For GCash merchant account issues:
- GCash Hotline: +63 (2) 8882-4274
- GCash Business: https://www.gcash.com/business/

For technical implementation issues:
- Check Laravel logs: `storage/logs/laravel.log`
- Review browser console for errors

---

**Ready to Go!** Once your QR is uploaded and displays correctly, the GCash Scan-to-Pay feature is fully operational.
