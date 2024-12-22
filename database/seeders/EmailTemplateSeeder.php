<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'subject'  => 'Connect us for business registration',
                'description'  => '
                    <p>Dear  {client_name}, this is to confirm that our tax services
                    have been successfully initiated. We will keep you
                    updated on the progress. Thank you for choosing
                    us.</p>',
                'type'  => '1',
                'form_type'  => 'business_registration',
            ],
            [
                'subject'  => 'Connect us for business registration',
                'description'  => "<h2>Dear {client_name},</h2>
                    <p>Registration Service Confirmed! TaxDunia's team will review your documents, prepare your Registration, and keep you updated.</p>
                    <p>Draft review and final submission to follow.</p>

                    <h3>How TaxDunia Works?</h3>
                    <ul>
                        <li>We review your documents and understand your needs.</li>
                        <li>Our experts prepare a customized solution.</li>
                        <li>We share the draft with you for review and feedback.</li>
                        <li>After your approval, we finalize and implement the solution.</li>
                    </ul>

                    <a href='#' class='button'>ATTACH PAYMENT RECEIPT</a>",
                'type'  => '2',
                'form_type'  => 'business_registration',
            ],
            [
                'subject'  => 'Connect us for business registration',
                'description'  => "<h2>Dear {client_name},</h2>
                    <p>Registration Service Confirmed! TaxDunia's team will review your documents, prepare your Registration, and keep you updated.</p>
                    <p>Draft review and final submission to follow.</p>

                    <h3>How TaxDunia Works?</h3>
                    <ul>
                        <li>We review your documents and understand your needs.</li>
                        <li>Our experts prepare a customized solution.</li>
                        <li>We share the draft with you for review and feedback.</li>
                        <li>After your approval, we finalize and implement the solution.</li>
                    </ul>

                    <a href='#' class='button'>ATTACH PAYMENT RECEIPT</a>",
                'type'  => '3',
                'form_type'  => 'business_registration',
            ],
        ];

        foreach( $data as $value){
            $check = EmailTemplate::where('type',$value['type'])->where('form_type',$value['form_type'])->first();
            if(empty($check)){
                EmailTemplate::create($value);
            }
        }
    }
}
