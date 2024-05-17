<x-filament::page :widget-data="['record' => $record]" :class="\Illuminate\Support\Arr::toCssClasses([
    'filament-resources-view-record-page',
    'filament-resources-' . str_replace('/', '-', $this->getResource()::getSlug()),
    'filament-resources-record-' . $record->getKey(),
])">

    <div>
        <table id='patient'>
            <tr>
                <td>PatientID</td>
                <td>:</td>
                <td>{{ $record->patientID }}</td>
            </tr>
            <tr>
                <td>Name</td>
                <td>:</td>
                <td>{{ $record->name }}</td>
            </tr>
            <tr>
                <td>Age</td>
                <td>:</td>
                <td>{{ $record->age }}</td>
            </tr>
            <tr>
                <td>Gender</td>
                <td>:</td>
                <td>{{ $record->gender }}</td>
            </tr>
            <tr>
                <td>Phone1</td>
                <td>:</td>
                <td>{{ $record->phone1 }}</td>
            </tr>
            <tr>
                <td>Phone2</td>
                <td>:</td>
                <td>{{ $record->phone2 }}</td>
            </tr>
        </table>
    </div>
    <div class="container mt-5">
        @if (isset($record->treatments))
            @php
                $treatments = $record->treatments;
            @endphp
            @foreach ($treatments as $index => $treatment)
                @php
                    $payments = $treatment->payments;
                    $attachments = $treatment->attachments;
                @endphp
                <div class="card custom-card">
                    <div class="card-header" id="heading{{ $index }}">
                        <h2 class="mb-0">
                            <button class="accordion-toggle" onclick="toggleAccordion('content{{ $index }}')">
                                Treatment on {{ $treatment->treatment_date }} by Dr. {{ $treatment->doctor->name }}
                            </button>
                            <button class="btn-icon" onclick="toggleImages('images{{ $index }}')">Show
                                Images</button>
                        </h2>
                    </div>

                    <div id="content{{ $index }}" class="accordion-content">
                        <div class="card-body custom-card-body">
                            <table id="treatment">
                                <tr>
                                    <th>Treatment Date</th>
                                    <th>Doctor Name</th>
                                    <th>Treatment Charges</th>
                                    <th>Xray Fees</th>
                                    <th>Medication Fees</th>
                                    <th>Total</th>
                                    <th>Paid</th>
                                    <th>Debt</th>
                                </tr>
                                <tr>
                                    <td>{{ $treatment->treatment_date }}</td>
                                    <td>{{ $treatment->doctor->name }}</td>
                                    <td>{{ $treatment->treatment_charges }}</td>
                                    <td>{{ $treatment->xray_fees??0 }}</td>
                                    <td>{{ $treatment->medication_fees??0  }}</td>
                                    <td>{{ $treatment->total }}</td>
                                    <td>{{ $treatment->payments->sum('amount') }}</td>
                                    <td>{{ $treatment->total - $treatment->payments->sum('amount') }}</td>
                                </tr>
                            </table>
                            <table id="payment">
                                <tr>
                                    <th>Paid Date</th>
                                    <th>Paid Amount</th>
                                </tr>
                                @foreach ($payments as $payment)
                                <tr>
                                    <td>
                                        {{ $payment->paid_date }}
                                    </td>
                                    <td>
                                        {{ $payment->amount }}
                                    </td>
                                </tr>

                                @endforeach
                            </table>

                            <div style="color: black;margin:3px 0">
                                <strong>Treatment Type</strong>
                                <p>{{ $treatment->treatment_type }}</p>
                            </div>
                            <div style="color: black">
                                <strong>Diagnosis</strong>
                                <p>{{ $treatment->diagnosis }}</p>
                            </div>
                            <div id="images{{ $index }}" class="images-container" style="display: none;margin-top:20px">
                                @foreach ($attachments as $attachment)
                                    <img class="custom-card-img" src="{{ asset('storage/' . $attachment->location) }}"
                                        alt="" title="" width="100px" height="100px" />
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>




</x-filament::page>
<script>
    function toggleAccordion(id) {
        var content = document.getElementById(id);
        console.log(content);
        if (content.style.display == "block") {
            content.style.display = "none";
        } else {
            content.style.display = "block";
        }
    }

    function toggleImages(id) {
        var images = document.getElementById(id);
        if (images.style.display === "") {
            images.style.display = "none";
        } else {
            images.style.display = "";
        }
    }
</script>
<style>
    .container {
        width: 80%;
        margin: auto;
    }

    .custom-card {
        /* border: 2px solid #1f2937; */
        /* border-radius: 15px; */
        margin-bottom: 1rem;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s;
    }

    .custom-card:hover {
        transform: scale(1.08);
    }

    .card-header {
        background-color: #2d3847;
        color: white;
        padding: 10px 20px;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
        cursor: pointer;
    }

    .accordion-toggle {
        background: none;
        border: none;
        color: white;
        font-size: 1.2rem;
        text-align: left;
        width: 90%;
    }

    .btn-icon {
        background: none;
        border: none;
        color: white;
        font-size: 1rem;
        cursor: pointer;
    }

    .accordion-content {
        display: none;
        padding: 0 20px 20px 20px;
        border-top: 1px solid black;
        background-color: #f8f9fa;
        border-bottom-left-radius: 15px;
        border-bottom-right-radius: 15px;
    }

    .custom-card-img {
        margin: 5px;
        border-radius: 5px;
    }
    #patient td {
        padding: 10px
    }
    #treatment {
        width: 100%;
        margin-bottom: 1rem;
        border-collapse: collapse;
        color: black;
    }

    #treatment th,
    #treatment td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
        color: black;
    }

    #treatment th {
        /* background-color: #007bff; */
        color: black;
    }
    #payment {
        width: 100%;
        margin-bottom: 1rem;
        border-collapse: collapse;
        color: black;
    }

    #payment th,
    #payment td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
        color: black;
    }

    #payment th {
        /* background-color: #007bff; */
        color: black;
    }
    #info{
        color: black;
        padding: 10px;
    }
    #info td{
        vertical-align: top
    }
    .images-container {
        display: flex;
        flex-wrap: wrap;
        margin-top: 10px;
    }
</style>
{{-- @foreach ($record->attachments as $item)
    <?php $itemData = json_decode($item, true); ?>

    <p>{{ $itemData['location'] }}</p>
    <img src="{{ asset('storage/'.$itemData['location']) }}" alt="a">
    <img src="{{ url('storage/'.$itemData['location']) }}" alt="b" title="" />
    <img src="{{ storage_path($itemData['location']) }}" alt="c" title="" />

@endforeach --}}
