<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax Invoice</title>
    <style>
        @page {
            margin: 0; /* Removes default page margin */
        }

        body {
            margin: 20px;
            padding: 0;
            font-family: Arial, sans-serif;
            font-size: 14px;
            background-color: #ffffff;
            color: #333;
            width: 200mm;
            border: #333 solid  1px;
            /* height: 1123px; */
        }

        table {
            border-spacing: 0 !important;
        }
    </style>
</head>
<body>

    <table border="1" style="  border-collapse: collapse; " >
        <tr>
            <td colspan="7" style="text-align: center;"><h3>TAX INVOICE</h3></td>
        </tr>
        <tr>
            {{-- <td width="60%"> --}}
                {{-- <table>
                    <tr> --}}
                        {{-- <td style="width: 25%;padding: 5px;"><img src="{{ public_path("Primary-Logo-Copy.png") }}" width="100%"/>  </td> --}}
                        <td style="width: 15%;padding: 5px;">
                            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path("Primary-Logo-Copy.png"))) }}" width="100%"/>
                        </td>
                        <td style="padding: 5px;" colspan="3">
                            <strong>PAPERLESS RACK DIGITAL SOLUTIONS PVT LTD</strong><br>
                            96, Arjun Nagar, Behind Dalda Factory<br>
                            Durgapura, Jaipur<br>
                            GSTIN/UIN: 08AAICP3355JZB<br>
                            State Name: Rajasthan, Code: 08<br>
                            CIN: AAICP3355J<br>
                            PAN: AAICP3335J<br>
                            E-Mail: info@kboo.org
                        </td>
                    {{-- </tr>
                </table> --}}
            {{-- </td> --}}
            <td  colspan="4" style="padding: 0px;">
               <table rowspan="2" border="1" style="width: 100%;
               border-collapse: collapse;
               height: 230px;">
                <tr>
                    <td  style="padding: 5px;">
                        Invoice No.</br>
                        <strong>{{ $data['invoice_id'] }}</strong>
                    </td>
                    <td  style="padding: 5px;">
                        Date</br>
                        <strong>{{$data['date']}}</strong>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding: 5px;">
                        <span>Customer Details: </span></br>
                        <span> Customer name: {{$data['buyer_name']}} </span></br>
                        <span >Customer Address: </span> </br>
                        <span>GSTIN/UIN : </span> </br>
                        <span>PAN: </span></br>
                        <span>State Name :</span> </br>
                        <span>Code : </span>
                    </td>
                </tr>
                {{-- <tr>
                    <td>
                        Order No.</br>
                        <strong>PLR/24-25/1</strong>
                    </td>
                    <td>
                        Dated</br>
                        <strong>PLR/24-25/1</strong>
                    </td>
                </tr>
                <tr>
                    <td>
                        Despatch Doc No</br>
                        <!-- <strong>PLR/24-25/1</strong> -->
                    </td>
                    <td>
                        Dated</br>
                        <!-- <strong>PLR/24-25/1</strong> -->
                    </td>
                </tr>
                <tr>
                    <td>
                        Dated</br>
                        <!-- <strong>PLR/24-25/1</strong> -->
                    </td>
                    <td>
                        Destination</br>
                        <!-- <strong>PLR/24-25/1</strong> -->
                    </td>
                </tr> --}}
               </table>
            </td>
        </tr>
        {{-- <tr>
            <td colspan="7" style="padding: 5px;">
                <span>Buyer :</span></br>
                <span> Buyer name :{{$data['buyer_name']}} </span></br>
                <span >buyer address GSTIN/UIN :</span><span>PAN/IT No : </span></br>
                <span>State Name :</span>   <span>Code : </span>
            </td>
        </tr> --}}
        <tr style="height: 25px;">
            <td colspan="4"  width="70%" style="text-align: center;">Particulars</td>
            <td width="20%" colspan="1" >HSN/SAC</td>
            <td width="20%" colspan="3">Amount</td>
        </tr>
        @php
             $count = 8;
        @endphp
        @if (isset($data['coupon']) && $data['coupon'] !="")
            $count = 7;
        @endif
        @for ($i = 0; $i < $count; $i++)
            @if(isset($data['plans'][$i]['name']))
                <tr class= "jk">
                    <td colspan="4"  width="60%" style="height: 46px;padding-left: 30px; border-top: none;border-bottom: none;">{{$data['plans'][$i]['name']}}</td>
                    <td width="20%" colspan='1' style="border-top: none;border-bottom: none;">998396</td>
                    <td width="20%" colspan="3" style="border-top: none;border-bottom: none;"><strong>{{$data['plans'][$i]['amount']}}</strong></td>
                </tr>
            @else
                <tr class= "jk">
                    <td colspan="4"  width="60%" style="height: 46px;padding-left: 30px; border-top: none;border-bottom: none;"></td>
                    <td width="20%" colspan='1' style="border-top: none;border-bottom: none;"></td>
                    <td width="20%" colspan="3" style="border-top: none;border-bottom: none;"><strong></strong></td>
                </tr>
            @endif
        @endfor
        @if (isset($data['lessAmount']) && $data['lessAmount'] > 0)
            <tr style="height: 25px; border:non;">
                <td  colspan="4" width="60%" style="text-align: right;">Coupon Discount / Round OFF</td>
                <td width="20%" colspan="1"></td>
                <td width="20%" colspan="3"><strong>{{$data['lessAmount']}}</strong></td>
            </tr>
        @endif
        <tr style="height: 25px;">
            <td  colspan="4" width="60%" style="text-align: right;"><b>CGST</b></td>
            <td width="20%" colspan="1"></td>
            <td width="20%" colspan="3"><strong>{{$data['cgst']}}</strong></td>
        </tr>
        <tr style="height: 25px;">
            <td  colspan="4" width="60%" style="text-align: right;"><b>SGST</b></td>
            <td width="20%" colspan="1"></td>
            <td width="20%" colspan="3"><strong>{{$data['sgst']}}</strong></td>
        </tr>
        <tr style="height: 25px;">
            <td  colspan="4" width="60%" style="text-align: right;">Total</td>
            <td width="20%" colspan="1"></td>
            <td width="20%" colspan="3"><strong>{{$data['totalAmount']}}</strong></td>
        </tr>
        <tr style="height: 50px;">
            <td colspan="5"  style="text-align: left;border-left: none;border-right: none; padding: 5px;">Amount Chargeable (in words) <br><strong>{{numberToWords($data['totalAmount'])}} Rupee Only</strong> </td>
            <td colspan="3" style="text-align: right;border-left: none;border-right: none; padding: 5px;">E. & O.E</td>
        </tr>
        <tr style="">
            <td rowspan="2">HSN/SAC</td>
            <td rowspan="2" style="font-size: 12px;">Taxable Value</td>
            <td colspan="2" style="font-size: 12px;">CGST</td>
            <td colspan="2" style="font-size: 12px;">SGST/UTGST</td>
            <td rowspan="2" colspan="2"style="font-size: 12px;">Total Tax Amount</td>

        </tr>
        <tr style="">
            <td style="font-size: 12px;">Rate</td>
            <td style="font-size: 12px;">Amount</td>
            <td style="font-size: 12px;">Rate</td>
            <td style="font-size: 12px;">Amount</td>
        </tr>
        <tr style="">
            <td style="width:0%;">998396</td>
            <td>{{$data['totalAmount']}}</td>
            <td>9%</td>
            <td>{{$data['cgst']}}</td>
            <td>9%</td>
            <td>{{$data['sgst']}}</td>
            <td colspan="2">{{$data['totalTax']}}</td>
        </tr>
        <tr style="">
            <td style="width:0%; text-align: right;"><strong>Total</strong></td>
            <td><strong>{{$data['totalAmount']}}</strong></td>
            <td></td>
            <td> <strong>{{$data['cgst']}}</strong></td>
            <td></td>
            <td> <strong>{{$data['sgst']}}</strong></td>
            <td colspan="2"> <strong>{{$data['totalTax']}}</strong></td>
        </tr>
        <tr>
            <td colspan="8" style="padding: 5px;">
                Tax Amount (in words)  : <strong> {{numberToWords($data['totalTax'])}} Rupee Only</strong><br>
                Company’s Bank Details :<br>
                Bank Name:   <b>Kotak Bank</b><br>
                A/c No. :  <b>9849146481</b> <br>
                Branch & IFS Code:  <b>KKBK0003559</b>
            </td>
        </tr>
        <tr style="height: 75px;">
            {{-- <td colspan="1"  style="padding: 5px;">
                Company’s PAN:AAICP3335J
            </td> --}}
            <td colspan="8" style="padding: 5px;">
                <b>for PAPERLESS RACK DIGITAL SOLUTIONS PVT LTD</b><br><br><br>

                <div style="text-align: right;">
                    <span>Authorised Signatory</span>
                </div>
            </td>
        </tr>
        <tr style="border-bottom:none;">
            <td colspan="8" style="text-align: center;">This is a Computer Generated Invoice</td>
        </tr>
    </table>
</body>
</html>
