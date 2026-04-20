<?php

namespace App\Http\Requests;

use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermissionTo('facture.payment');
    }

    public function rules(): array
    {
        return [
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date|before_or_equal:today',
            'payment_method' => 'nullable|string|max:50',
            'note' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'invoice_id.required' => 'La facture est obligatoire.',
            'invoice_id.exists' => 'La facture sélectionnée n\'existe pas.',
            'amount.required' => 'Le montant est obligatoire.',
            'amount.min' => 'Le montant doit être supérieur à 0.',
            'payment_date.required' => 'La date de paiement est obligatoire.',
            'payment_date.before_or_equal' => 'La date de paiement ne peut pas être dans le futur.',
        ];
    }

    /**
     * Configure the validator instance.
     * Add custom validation rule: amount <= invoice.balance
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $invoiceId = $this->input('invoice_id');
            $amount = (float) $this->input('amount', 0);

            if (!$invoiceId || $amount <= 0) {
                return;
            }

            $invoice = Invoice::find($invoiceId);
            if (!$invoice) {
                return;
            }

            // Check if invoice is cancelled
            if ($invoice->status === 'ANNULEE') {
                $validator->errors()->add(
                    'invoice_id',
                    'Impossible d\'enregistrer un paiement sur une facture annulée.'
                );
                return;
            }

            // Custom rule: amount <= invoice.balance
            if ($amount > $invoice->balance) {
                $validator->errors()->add(
                    'amount',
                    'Le montant de ' . number_format($amount, 2) . ' FCFA dépasse le solde restant de ' . number_format($invoice->balance, 2) . ' FCFA'
                );
            }
        });
    }
}
