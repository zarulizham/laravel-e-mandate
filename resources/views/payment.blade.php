<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css"
        integrity="undefined" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;display=swap" rel="stylesheet">
    <link href="{{ asset('assets/vendor/fpx-payment/css/form-validation.css') }}" rel="stylesheet">

    <style>
        body {
            font-family: 'Roboto', sans-serif
        }

        .btn-primary {
            background: #263A56
        }

        .btn-primary:hover {
            background: #4EA4F8
        }

        .table-sm {
            font-size: 10pt;
        }

        textarea:hover,
        input:hover,
        textarea:active,
        input:active,
        textarea:focus,
        input:focus,
        button:focus,
        button:active,
        button:hover,
        label:focus,
        select:focus,
        select:active,
        .btn:active,
        .btn.active {
            outline: 0px !important;
            -webkit-appearance: none !important;
            box-shadow: none !important;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="pt-5 pb-2 text-center">
            <img src="{{ config('app.aspire_logo_url') }}" alt="" height="60">
        </div>

        <div class="text-center pb-5">
            <small class="text-muted">{{ $bill->hash_id }}</small>
        </div>

        <form class="needs-validation" novalidate method="POST" action="{{ route('e-mandate.payment.auth.request') }}">
            @csrf
            <input type="hidden" name="response_format" value="{{ $response_format }}" />
            <input type="hidden" name="reference_id" value="{{ $bill->hash_id . rand() }}" />
            <input type="hidden" name="additional_params" value="{{ $bill->remarks }}" />
            {{-- <input type="hidden" name="reference_id" value="BILL_HASH_ID" /> --}}
            {{-- <input type="hidden" name="additional_params" value="REMARKS" /> --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ implode(',', $errors->all()) }}
                </div>
            @endif
            <div class="row">
                <div class="offset-sm-1 offset-md-2 offset-lg-3 col-md-8 col-lg-6 col-sm-10 order-md-2 mb-4">
                    <div class="border p-3 mb-3 rounded">
                        <h4 class="mb-4">Payment Details</h4>
                        <div class="row align-items-center">
                            <div class="col-4">
                                <label for="inputPassword6" class="col-form-label"><strong>Total Amount</strong></label>
                            </div>
                            <div class="col-auto">
                                <span class="form-control-plaintext">RM {{ number_format($bill->amount ?? 10, 2) }}</span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 col-sm-12">
                                <div class="custom-control custom-radio">
                                    {{-- <img src="{{ asset('assets/vendor/fpx-payment/Images/fpx.svg') }}" height="64px"> --}}
                                </div>
                            </div>
                        </div>

                        <div class="row align-items-center mt-2">
                            <div class="col-4">
                                <label for="flow" class="col-form-label"><strong>Bank Type</strong> <i data-bs-toggle="modal" data-bs-target="#bank_type_modal" class="fas fa-info-circle"></i> </label>
                            </div>
                            <div class="col">
                                <select name="flow" id="flow" class="form-control shadow-none w-100">
                                    <option {{ old('flow') == '01' ? 'selected' : '' }} value="01">Retail Banking - B2C</option>
                                    <option {{ old('flow') == '02' ? 'selected' : '' }} value="02">Corporate Banking - B2B</option>
                                </select>
                            </div>
                        </div>

                        <div class="row align-items-center mt-2">
                            <div class="col-4">
                                <label for="bank_id" class="col-form-label"><strong>Bank</strong></label>
                            </div>
                            <div class="col">
                                <select name="bank_id" id="bank_id" class="form-control">
                                    <option value="">Select Bank</option>
                                </select>
                            </div>
                        </div>

                        <button class="btn btn-primary btn-lg btn-block w-100 mt-4" type="submit">Proceed</button>
                        <div class="row mb-3">
                            <div class="col">
                                <div class="custom-control custom-checkbox text-center">
                                    <label class="custom-control-label text-muted text-center" for="agree" style="font-size: 6pt">By clicking on "Proceed" button, you agree to the <a href="https://www.mepsfpx.com.my/FPXMain/termsAndConditions.jsp" target="_blank">terms and conditions</a> of FPX.</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8 order-md-1 d-nonze">
                    <div class="border p-3 mb-3 rounded">
                        <h4 class="mb-3">Billing details</h4>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="customer_name">Buyer name</label>
                                <input type="text" class="form-control" id="customer_name" name="customer_name" readonly
                                    placeholder="Enter buyer name"
                                    value="{{ $bill->user->name }}" required>
                                <div class="invalid-feedback">
                                    Valid buyer name is required.
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="amount">Amount</label>
                            <input type="number" class="form-control" id="amount" name="amount" readonly
                                placeholder="1.00" value="{{ $bill->amount }}" required>
                            <div class="invalid-feedback">
                                Please enter a valid amount.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="customer_email">Email</label>
                            <input type="email" class="form-control" id="customer_email" readonly name="customer_email"
                                value="mardyoe@gmail.com"
                                placeholder="email@mbsp.gov.my" required>
                            <div class="invalid-feedback">
                                Please enter a valid email address.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="">buyerId</label>
                            <input type="text" class="form-control" name="buyerId" readonly value="941009036291,1">

                        </div>

                        <div class="mb-3">
                            <label for="buyerIban">buyerIban</label>
                            <input type="text" class="form-control" name="buyerIban" readonly value="01,0148220679,1,MT,141122,141123">
                        </div>

                        <div class="mb-3">
                            <label for="remark">Remark</label>
                            <textarea class="form-control" id="remark" name="remark"
                                placeholder="ASPIRE Seberang Perai Payments"
                                readonly>Pembayaran Resit</textarea>
                            <div class="invalid-feedback">
                                Please enter valid remark
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        @if ($bill->merchant)
            <div class="mb-3 text-center">
                <a style="font-size: 9pt" href="{{ $bill->merchant->redirect_url }}">Cancel Transaction</a>
            </div>
        @endif
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function() {
            'use strict';

            window.addEventListener('load', function() {
                // Fetch all the forms we want to apply custom Bootstrap validation styles to
                var forms = document.getElementsByClassName('needs-validation');

                // Loop over them and prevent submission
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();

        $("#bank_id").select2({
            ajax: {
                url: function() {
                    return "{{ route('api.banks.index') }}";
                },
                dataType: 'json',
                data: function(params) {
                    return {
                        type: $("#flow").val(),
                        name: params.term,
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data.banks, function(bank) {
                            return {
                                text: bank.name,
                                display: bank.name,
                                id: bank.bank_id,
                                disabled: bank.status == 'Offline',
                            }
                        }),
                    };
                }
            }
        })

        $("#flow").select2({
            minimumResultsForSearch: Infinity
        });
    </script>

    @include('vendor.fpx-payment.bank_type_modal')
</body>

</html>
