<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VatInvoice extends Model {
    protected $table = 'vat_invoices';

    protected $fillable = [
        'customer_id', 'short_name', 'business_name', 'buyer_name',
        'tax_code', 'invoice_address', 'invoice_email', 'receiver_name', 'status'
    ];

    public function customer() {
        return $this->belongsTo(User::class, 'customer_id');
    }
}
