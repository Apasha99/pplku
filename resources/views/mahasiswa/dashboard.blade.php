@extends('mahasiswa.layouts.layout2')

@section('content')
    <div class="mb-4 col-span-full xl:mb-2">
        <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Welcome, mahasiswa!</h1>
    </div>

    <div class="col-span-full xl:col-auto">
        <div
            class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm 2xl:col-span-2 dark:border-gray-700 sm:p-6 dark:bg-gray-800">
            <div class="items-center sm:flex xl:block 2xl:flex sm:space-x-4 xl:space-x-0 2xl:space-x-4">
                <img src="{{ Auth::user()->getImageURL() }}" class="mb-4 rounded-lg w-28 h-28 sm:mb-0 xl:mb-4 2xl:mb-0"
                    alt="foto-profil" />
                <div>
                    <h3 class="mb-1 text-xl font-bold text-gray-900 dark:text-white">{{ $mahasiswa->nama }}</h3>
                    <div class="mb-4 text-sm text-gray-500 dark:text-gray-400">
                        <p>{{ $mahasiswa->nim }}</p>
                        <p>INFORMATIKA</p>
                        <p>{{ $mahasiswa->angkatan }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-span-2">
        <div
            class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm 2xl:col-span-2 dark:border-gray-700 sm:p-6 dark:bg-gray-800">
            <div class="flow-root">
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    <li class="py-4">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <span class="text-gray-900 truncate dark:text-white">
                                    <i class="fa-solid fa-building-columns fa-xl"></i>
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-base font-semibold text-gray-900 truncate dark:text-white">
                                    Fakultas
                                </p>
                                <p class="text-sm font-normal text-gray-500 truncate dark:text-gray-400">
                                    Sains dan Matematika
                                </p>
                            </div>
                        </div>
                    </li>
                    <li class="pt-4 pb-6">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 dark:text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-base font-semibold text-gray-900 truncate dark:text-white">
                                    Dosen Wali
                                </p>
                                <p class="text-sm font-normal text-gray-500 truncate dark:text-gray-400">
                                    {{ $mahasiswa->dosen_nama }}
                                </p>
                            </div>
                        </div>
                    </li>
                    <li class="pt-4 pb-6">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 dark:text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-base font-semibold text-gray-900 truncate dark:text-white">
                                    Semester Aktif
                                </p>
                                <p class="text-sm font-normal text-gray-500 truncate dark:text-gray-400">
                                    {{ $SemesterAktif }}
                                </p>
                            </div>
                        </div>
                    </li>
                    <li class="pt-6 pb-4 ">
                        <div class="flex items-center space-x-8">
                            <div class="form-group"style="font-size: 20px;">
                               
                            </div>
                            <div class="block flex-2 min-w-2">
                                @for ($i = 1; $i <= 14; $i++)
                                    @php
                                        $irs = $irsData[$i] ?? null;
                                        $khs = $khsData[$i] ?? null;
                                        $pkl = $pklData[$i] ?? null;
                                        $skripsi = $skripsiData[$i] ?? null;
                                        $lastVerifiedPKL;

                                        $cardClass = 'bg-red-400'; // Default color is red

                                        if ($irs && !$khs && !$pkl && !$skripsi) {
                                            if ($irs->status == 'verified') {
                                                $cardClass = 'bg-blue-300'; // Light blue if only IRS is 'verified'
                                            }
                                        } elseif ($irs && $khs && !$pkl && !$skripsi) {
                                            if ($irs->status == 'verified' && $khs->status == 'verified') {
                                                $cardClass = 'bg-blue-800'; // Dark blue if IRS and KHS are 'verified'
                                            }
                                        } elseif ($irs && $khs && $pkl && !$skripsi ) {
                                            if ($irs->status == 'verified' && $khs->status == 'verified' && $pkl->status == 'verified') {
                                                $cardClass = 'bg-yellow-400'; // Yellow if IRS, KHS, and PKL are 'verified'
                                            }
                                        } elseif ($irs && $khs && !$pkl && $skripsi && $lastVerifiedPKL && $lastVerifiedPKL->semester_aktif != $irs->semester_aktif) {
                                            if ($irs->status == 'verified' && $khs->status == 'verified' && $skripsi->status == 'verified' && $lastVerifiedPKL) {
                                                $cardClass = 'bg-green-400'; // Green if all statuses are 'verified'
                                            }
                                        }

                                    @endphp

                                    <!-- Tampilkan tombol dengan warna sesuai status -->
                                    <a data-modal-target="modal-{{ $i }}" data-modal-toggle="modal-{{ $i }}" class="text-white {{ $cardClass }} box-border h-10 w-30 card p-2 overflow-hidden">{{ $i }}</a>

                                    <!-- Modal for each status -->
                                    <div class="fixed left-0 right-0 z-50 items-center justify-center hidden overflow-x-hidden overflow-y-auto top-4 md:inset-0 h-modal sm:h-full "
                                        id="modal-{{ $i }}">
                                        <div class="relative w-full h-full max-w-2xl px-4 md:h-auto">
                                            <!-- Modal content -->
                                            <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
                                                <!-- Modal header -->
                                                <div class="flex items-start justify-between p-5 border-b rounded-t dark:border-gray-700">
                                                    <h3 class="text-xl font-semibold dark:text-white">
                                                        Semester - {{$i}}
                                                    </h3>
                                                    <button type="button"
                                                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-700 dark:hover:text-white"
                                                        data-modal-toggle="modal-{{ $i }}">
                                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd"
                                                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                                clip-rule="evenodd"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                                <!-- Modal body -->
                                                @if ($cardClass == 'bg-blue-300' && $irs)
                                                <div class="my-4 border-b border-gray-200 dark:border-gray-700">
                                                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="fullWidthTab" data-tabs-toggle="#fullWidthTabContent" role="tablist">
                                                        <li class="me-2" >
                                                            <button id="irs-tab-{{ $i }}" data-tabs-target="#irs-{{ $i }}" type="button" role="tab" aria-controls="irs" aria-selected="true" class="inline-block py-2 px-5 border-b-2 rounded-t-lg">IRS</button>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div id="fullWidthTabContent" >
                                                    <div class="hidden pt-2 pb-5 mb-5 ml-5 mr-5" id="irs-{{$i}}" role="tabpanel" aria-labelledby="irs-tab">
                                                    <table class="table-auto border-collapse w-full ">
                                                        <tbody>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Semester Aktif</td>
                                                                <td class="border px-2 py-2 text-white">{{ $irs->semester_aktif }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Jumlah SKS</td>
                                                                <td class="border px-2 py-2 text-white">{{ $irs->jumlah_sks }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Scan IRS</td>
                                                                <td class="border px-2 py-2 ">
                                                                <a href="{{ asset('storage/' . $irs->scanIRS) }}" target="_blank" class="text-sm font-semibold text-blue-500 ">Lihat IRS</a>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        
                                                    </table>
                                                    </div>
                                                </div>
                                                @endif
                                                @if ($cardClass == 'bg-blue-800' && $irs && $khs)
                                                <div class="my-4 border-b border-gray-200 dark:border-gray-700">
                                                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="fullWidthTab" data-tabs-toggle="#fullWidthTabContent" role="tablist">
                                                        <li class="me-2" >
                                                            <button id="irs-tab-{{ $i }}" data-tabs-target="#irs-{{ $i }}" type="button" role="tab" aria-controls="irs" aria-selected="true" class="inline-block py-2 px-5 border-b-2 rounded-t-lg">IRS</button>
                                                        </li>
                                                        <li class="me-2" >
                                                            <button id="khs-tab-{{ $i }}" data-tabs-target="#khs-{{ $i }}" type="button" role="tab" aria-controls="khs" aria-selected="true" class="inline-block py-2 px-5 border-b-2 rounded-t-lg">KHS</button>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div id="fullWidthTabContent" >
                                                    <div class="hidden pt-2 pb-5 mb-5 ml-5 mr-5" id="irs-{{$i}}" role="tabpanel" aria-labelledby="irs-tab">
                                                    <table class="table-auto border-collapse w-full ">
                                                        <tbody>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Semester Aktif</td>
                                                                <td class="border px-2 py-2 text-white">{{ $irs->semester_aktif }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Jumlah SKS</td>
                                                                <td class="border px-2 py-2 text-white">{{ $irs->jumlah_sks }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Scan IRS</td>
                                                                <td class="border px-2 py-2 ">
                                                                <a href="{{ asset('storage/' . $irs->scanIRS) }}" target="_blank" class="text-sm font-semibold text-blue-500 ">Lihat IRS</a>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        
                                                    </table>
                                                    </div>
                                                </div>
                                                <div id="fullWidthTabContent" >
                                                    <div class="hidden pt-2 pb-5 mb-5 ml-5 mr-5" id="khs-{{$i}}" role="tabpanel" aria-labelledby="khs-tab">
                                                    <table class="table-auto border-collapse w-full ">
                                                        <tbody>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Semester Aktif</td>
                                                                <td class="border px-2 py-2 text-white">{{ $khs->semester_aktif }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Jumlah SKS</td>
                                                                <td class="border px-2 py-2 text-white">{{ $khs->jumlah_sks }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Jumlah SKS Kumulatif</td>
                                                                <td class="border px-2 py-2 text-white">{{ $khs->jumlah_sks_kumulatif }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">IP Semester</td>
                                                                <td class="border px-2 py-2 text-white">{{ $khs->ip_semester }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Jumlah SKS</td>
                                                                <td class="border px-2 py-2 text-white">{{ $khs->ip_kumulatif }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Scan KHS</td>
                                                                <td class="border px-2 py-2 ">
                                                                <a href="{{ asset('storage/' . $khs->scanKHS) }}" target="_blank" class="text-sm font-semibold text-blue-500 ">Lihat KHS</a>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        
                                                    </table>
                                                    </div>
                                                </div>
                                                @endif
                                                @if ($cardClass == 'bg-yellow-400' && $irs && $khs && $pkl)
                                                <div class="my-4 border-b border-gray-200 dark:border-gray-700">
                                                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="fullWidthTab" data-tabs-toggle="#fullWidthTabContent" role="tablist">
                                                        <li class="me-2" >
                                                            <button id="irs-tab-{{ $i }}" data-tabs-target="#irs-{{ $i }}" type="button" role="tab" aria-controls="irs" aria-selected="true" class="inline-block py-2 px-5 border-b-2 rounded-t-lg">IRS</button>
                                                        </li>
                                                        <li class="me-2" >
                                                            <button id="khs-tab-{{ $i }}" data-tabs-target="#khs-{{ $i }}" type="button" role="tab" aria-controls="khs" aria-selected="true" class="inline-block py-2 px-5 border-b-2 rounded-t-lg">KHS</button>
                                                        </li>
                                                        <li class="me-2" >
                                                            <button id="pkl-tab-{{ $i }}" data-tabs-target="#pkl-{{ $i }}" type="button" role="tab" aria-controls="pkl" aria-selected="true" class="inline-block py-2 px-5 border-b-2 rounded-t-lg">PKL</button>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div id="fullWidthTabContent" >
                                                    <div class="hidden pt-2 pb-5 mb-5 ml-5 mr-5" id="irs-{{$i}}" role="tabpanel" aria-labelledby="irs-tab">
                                                    <table class="table-auto border-collapse w-full ">
                                                        <tbody>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Semester Aktif</td>
                                                                <td class="border px-2 py-2 text-white">{{ $irs->semester_aktif }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Jumlah SKS</td>
                                                                <td class="border px-2 py-2 text-white">{{ $irs->jumlah_sks }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Scan IRS</td>
                                                                <td class="border px-2 py-2 ">
                                                                <a href="{{ asset('storage/' . $irs->scanIRS) }}" target="_blank" class="text-sm font-semibold text-blue-500 ">Lihat IRS</a>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        
                                                    </table>
                                                    </div>
                                                </div>
                                                <div id="fullWidthTabContent" >
                                                    <div class="hidden pt-2 pb-5 mb-5 ml-5 mr-5" id="khs-{{$i}}" role="tabpanel" aria-labelledby="khs-tab">
                                                    <table class="table-auto border-collapse w-full ">
                                                        <tbody>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Semester Aktif</td>
                                                                <td class="border px-2 py-2 text-white">{{ $khs->semester_aktif }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Jumlah SKS</td>
                                                                <td class="border px-2 py-2 text-white">{{ $khs->jumlah_sks }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Jumlah SKS Kumulatif</td>
                                                                <td class="border px-2 py-2 text-white">{{ $khs->jumlah_sks_kumulatif }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">IP Semester</td>
                                                                <td class="border px-2 py-2 text-white">{{ $khs->ip_semester }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Jumlah SKS</td>
                                                                <td class="border px-2 py-2 text-white">{{ $khs->ip_kumulatif }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Scan KHS</td>
                                                                <td class="border px-2 py-2 ">
                                                                <a href="{{ asset('storage/' . $khs->scanKHS) }}" target="_blank" class="text-sm font-semibold text-blue-500 ">Lihat KHS</a>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                        
                                                    </table>
                                                    </div>
                                                </div>
                                                <div id="fullWidthTabContent" >
                                                    <div class="hidden pt-2 pb-5 mb-5 ml-5 mr-5" id="pkl-{{$i}}" role="tabpanel" aria-labelledby="pkl-tab">
                                                    <table class="table-auto border-collapse w-full ">
                                                        <tbody>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Semester Aktif</td>
                                                                <td class="border px-2 py-2 text-white">{{ $pkl->semester_aktif }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Nilai</td>
                                                                <td class="border px-2 py-2 text-white">{{ $pkl->nilai }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Scan PKL</td>
                                                                <td class="border px-2 py-2 ">
                                                                <a href="{{ asset('storage/' . $pkl->scanPKL) }}" target="_blank" class="text-sm font-semibold text-blue-500 ">Lihat PKL</a>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        
                                                    </table>
                                                    </div>
                                                </div>
                                                @endif
                                                @if ($cardClass == 'bg-green-400' && $irs && $khs && $skripsi && $lastVerifiedPKL && $lastVerifiedPKL->semester_aktif != $irs->semester_aktif)
                                                <div class="my-4 border-b border-gray-200 dark:border-gray-700">
                                                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="fullWidthTab" data-tabs-toggle="#fullWidthTabContent" role="tablist">
                                                        <li class="me-2" >
                                                            <button id="irs-tab-{{ $i }}" data-tabs-target="#irs-{{ $i }}" type="button" role="tab" aria-controls="irs" aria-selected="true" class="inline-block py-2 px-5 border-b-2 rounded-t-lg">IRS</button>
                                                        </li>
                                                        <li class="me-2" >
                                                            <button id="khs-tab-{{ $i }}" data-tabs-target="#khs-{{ $i }}" type="button" role="tab" aria-controls="khs" aria-selected="true" class="inline-block py-2 px-5 border-b-2 rounded-t-lg">KHS</button>
                                                        </li>
                                                        <li class="me-2" >
                                                            <button id="pkl-tab-{{ $i }}" data-tabs-target="#pkl-{{ $i }}" type="button" role="tab" aria-controls="pkl" aria-selected="true" class="inline-block py-2 px-5 border-b-2 rounded-t-lg">PKL</button>
                                                        </li>
                                                        <li class="me-2" >
                                                            <button id="skripsi-tab-{{ $i }}" data-tabs-target="#skripsi-{{ $i }}" type="button" role="tab" aria-controls="skripsi" aria-selected="true" class="inline-block py-2 px-5 border-b-2 rounded-t-lg">Skripsi</button>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div id="fullWidthTabContent" >
                                                    <div class="hidden pt-2 pb-5 mb-5 ml-5 mr-5" id="irs-{{$i}}" role="tabpanel" aria-labelledby="irs-tab">
                                                    <table class="table-auto border-collapse w-full ">
                                                        <tbody>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Semester Aktif</td>
                                                                <td class="border px-2 py-2 text-white">{{ $irs->semester_aktif }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Jumlah SKS</td>
                                                                <td class="border px-2 py-2 text-white">{{ $irs->jumlah_sks }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Scan IRS</td>
                                                                <td class="border px-2 py-2 ">
                                                                <a href="{{ asset('storage/' . $irs->scanIRS) }}" target="_blank" class="text-sm font-semibold text-blue-500 ">Lihat IRS</a>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        
                                                    </table>
                                                    </div>
                                                </div>
                                                <div id="fullWidthTabContent" >
                                                    <div class="hidden pt-2 pb-5 mb-5 ml-5 mr-5" id="khs-{{$i}}" role="tabpanel" aria-labelledby="khs-tab">
                                                    <table class="table-auto border-collapse w-full ">
                                                        <tbody>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Semester Aktif</td>
                                                                <td class="border px-2 py-2 text-white">{{ $khs->semester_aktif }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Jumlah SKS</td>
                                                                <td class="border px-2 py-2 text-white">{{ $khs->jumlah_sks }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Jumlah SKS Kumulatif</td>
                                                                <td class="border px-2 py-2 text-white">{{ $khs->jumlah_sks_kumulatif }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">IP Semester</td>
                                                                <td class="border px-2 py-2 text-white">{{ $khs->ip_semester }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Jumlah SKS</td>
                                                                <td class="border px-2 py-2 text-white">{{ $khs->ip_kumulatif }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Scan KHS</td>
                                                                <td class="border px-2 py-2 ">
                                                                <a href="{{ asset('storage/' . $khs->scanKHS) }}" target="_blank" class="text-sm font-semibold text-blue-500 ">Lihat KHS</a>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                        
                                                    </table>
                                                    </div>
                                                </div>
                                                <div id="fullWidthTabContent" >
                                                    <div class="hidden pt-2 pb-5 mb-5 ml-5 mr-5" id="pkl-{{$i}}" role="tabpanel" aria-labelledby="pkl-tab">
                                                    <table class="table-auto border-collapse w-full ">
                                                        <tbody>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Semester Aktif</td>
                                                                <td class="border px-2 py-2 text-white">{{ $lastVerifiedPKL->semester_aktif }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Nilai</td>
                                                                <td class="border px-2 py-2 text-white">{{ $lastVerifiedPKL->nilai }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Scan PKL</td>
                                                                <td class="border px-2 py-2 ">
                                                                <a href="{{ asset('storage/' . $lastVerifiedPKL->scanPKL) }}" target="_blank" class="text-sm font-semibold text-blue-500 ">Lihat PKL</a>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        
                                                    </table>
                                                    </div>
                                                </div>
                                                <div id="fullWidthTabContent" >
                                                    <div class="hidden pt-2 pb-5 mb-5 ml-5 mr-5" id="skripsi-{{$i}}" role="tabpanel" aria-labelledby="skripsi-tab">
                                                    <table class="table-auto border-collapse w-full ">
                                                        <tbody>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Semester Aktif</td>
                                                                <td class="border px-2 py-2 text-white">{{ $skripsi->semester_aktif }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Nilai</td>
                                                                <td class="border px-2 py-2 text-white">{{ $skripsi->nilai }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Lama Studi</td>
                                                                <td class="border px-2 py-2 text-white">{{ $skripsi->lama_studi }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Tanggal Sidang</td>
                                                                <td class="border px-2 py-2 text-white">{{ $skripsi->tanggal_sidang }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="border px-2 py-2 text-white">Scan Skripsi</td>
                                                                <td class="border px-2 py-2 ">
                                                                <a href="{{ asset('storage/' . $skripsi->scanSkripsi) }}" target="_blank" class="text-sm font-semibold text-blue-500 ">Lihat Skripsi</a>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        
                                                    </table>
                                                    </div>
                                                </div>
                                                @endif

                                            </div>
                                        </div>
                                    </div>

                                @endfor
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection

@section('content2')
    <div class="grid w-full grid-cols-1 gap-4 mt-4 xl:grid-cols-2 2xl:grid-cols-3">
        <div
            class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:flex dark:border-gray-700 sm:p-6 dark:bg-gray-800">
            <div class="w-full">
                <h3 class="text-base font-bold text-gray-900 dark:text-gray-400">IRS</h3>
                <span class="text-2xl mr-3 leading-none text-gray-900 sm:text-3xl dark:text-white">{{ $JumlahSKS }}
                    SKS</span>
                <p class="flex items-center text-base font-normal text-gray-500 dark:text-gray-400">
                    @if ($statusIRS === 'verified')
                        <span class="flex items-center mr-3 text-green-500 dark:text-green-400">
                            <i class="fa-solid fa-check mr-1"></i> {{ $statusIRS }}
                        </span>
                    @else
                        {{ $statusIRS }}
                    @endif
                </p>
            </div>
        </div>
        <div
            class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:flex dark:border-gray-700 sm:p-6 dark:bg-gray-800">
            <div class="w-full">
                <h3 class="text-base font-bold text-gray-900 dark:text-gray-400">KHS</h3>
                <span class="text-2xl  leading-none text-gray-900 sm:text-3xl dark:text-white">IPK
                    {{ $IPKumulatif }}</span>
                <p class="flex items-center text-base font-normal text-gray-500 dark:text-gray-400">
                    @if ($statusKHS === 'verified')
                        <span class="flex items-center mr-3 text-green-500 dark:text-green-400">
                            <i class="fa-solid fa-check mr-1"></i> {{ $statusKHS }}
                        </span>
                    @else
                        {{ $statusKHS }}
                    @endif
                </p>
            </div>
        </div>

        @if (!is_null($nilaiPKL))
        <div
            class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:flex dark:border-gray-700 sm:p-6 dark:bg-gray-800">
            <div class="w-full">
                <h3 class="text-base font-bold text-gray-900 dark:text-gray-400">PKL</h3>
                <span class="text-2xl  leading-none text-gray-900 sm:text-3xl dark:text-white">{{ $nilaiPKL }}</span>
                <p class="flex items-center text-base font-normal text-gray-500 dark:text-gray-400">
                    @if ($status === 'verified')
                        <span class="flex items-center mr-3 text-green-500 dark:text-green-400">
                            <i class="fa-solid fa-check mr-1"></i> {{ $status }}
                        </span>
                    @else
                        {{ $status }}
                    @endif
                </p>
            </div>
        </div>
        @endif

        @if (!is_null($nilaiSkripsi))
        <div
            class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:flex dark:border-gray-700 sm:p-6 dark:bg-gray-800">
            <div class="w-full">
                <h3 class="text-base font-bold text-gray-900 dark:text-gray-400">Skripsi</h3>
                <span class="text-2xl  leading-none text-gray-900 sm:text-3xl dark:text-white">{{ $nilaiSkripsi }}</span>
                <p class="flex items-center text-base font-normal text-gray-500 dark:text-gray-400">
                    @if ($statusSkr === 'verified')
                        <span class="flex items-center mr-3 text-green-500 dark:text-green-400">
                            <i class="fa-solid fa-check mr-1"></i> {{ $statusSkr }}
                        </span>
                    @else
                        {{ $statusSkr }}
                    @endif
                </p>
            </div>
        </div>
        @endif
    </div>
@endsection

{{-- @extends('mahasiswa.layouts.layout')

@section('content')
    <h3 class="text-center text-4xl font-medium text-gray-900 dark:text-white mb-6">Welcome, Mahasiswa</h3>

    <section class="bg-white dark:bg-gray-900 border border-gray-900 rounded-lg mb-6">
        <div class="py-8 px-4 mx-auto max-w-screen-xl text-center lg:py-16 lg:px-6">
            <div class="text-center text-gray-500 dark:text-gray-400">
                <div class="text-center text-gray-500 dark:text-gray-400">
                    <img class="mx-auto mb-4 w-36 h-36 rounded-full"
                        src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/bonnie-green.png"
                        alt="Bonnie Avatar">
                    <h3 class="mb-1 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                        <a href="#">Maya Hart</a>
                    </h3>
                    <p>2406121100037</p>
                    <p>Informatika 2021</p>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-gray-50">
        <div class="flex flex-wrap">
            <div class="mt-4 w-full lg:w-6/12 xl:w-3/12 px-5 mb-4">
            <div class="relative flex flex-col min-w-0 break-words bg-white rounded mb-3 xl:mb-0 shadow-lg">
                <div class="flex-auto p-4">
                <div class="flex flex-wrap">
                    <div class="relative w-full pr-4 max-w-full flex-grow flex-1">
                    <h5 class="text-blueGray-400 uppercase font-bold text-xs"> IRS</h5>
                    <span class="font-semibold text-xl text-blueGray-700">24 SKS</span>
                    </div>
                    <div class="relative w-auto pl-4 flex-initial">
                    <div class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full  bg-red-500">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    </div>
                </div>
                <p class="text-sm text-blueGray-400 mt-4">
                    <span class="text-emerald-500 mr-2"><i class="fa-solid fa-check"></i> AKTIF </span>
                    <span class="whitespace-nowrap"> pending </span></p>
                </div>
            </div>
            </div>
        
            <div class=" mt-4 w-full lg:w-6/12 xl:w-3/12 px-5">
            <div class="relative flex flex-col min-w-0 break-words bg-white rounded mb-4 xl:mb-0 shadow-lg">
                <div class="flex-auto p-4">
                <div class="flex flex-wrap">
                    <div class="relative w-full pr-4 max-w-full flex-grow flex-1">
                    <h5 class="text-blueGray-400 uppercase font-bold text-xs">KHS</h5>
                    <span class="font-semibold text-xl text-blueGray-700">100 SKS</span>
                    </div>
                    <div class="relative w-auto pl-4 flex-initial">
                    <div class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full  bg-pink-500">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    </div>
                </div>
                <p class="text-sm text-blueGray-400 mt-4">
                    <span class="text-red-500 mr-2"><i class="fas fa-arrow-down"></i> IPK 3.25 </span>
                    <span class="whitespace-nowrap"> approved </span></p>
                </div>
            </div>
            </div>
        
            <div class="mt-4 w-full lg:w-6/12 xl:w-3/12 px-5">
            <div class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                <div class="flex-auto p-4">
                <div class="flex flex-wrap">
                    <div class="relative w-full pr-4 max-w-full flex-grow flex-1">
                    <h5 class="text-blueGray-400 uppercase font-bold text-xs">PKL</h5>
                    <span class="font-semibold text-xl text-blueGray-700">A</span>
                    </div>
                    <div class="relative w-auto pl-4 flex-initial">
                    <div class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full  bg-lightBlue-500">
                        <i class="fas fa-users"></i>
                    </div>
                    </div>
                </div>
                <p class="text-sm text-blueGray-400 mt-4">
                    <span class="text-emerald-500 mr-2"><i class="fa-solid fa-check"></i> LULUS </span>
                    <span class="whitespace-nowrap"> approved </span></p>
                </div>
            </div>
            </div>
        
            <div class="mt-4 w-full lg:w-6/12 xl:w-3/12 px-5">
            <div class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                <div class="flex-auto p-4">
                <div class="flex flex-wrap">
                    <div class="relative w-full pr-4 max-w-full flex-grow flex-1">
                    <h5 class="text-blueGray-400 uppercase font-bold text-xs">Skripsi</h5>
                    <span class="font-semibold text-xl text-blueGray-700"> — </span>
                    </div>
                    <div class="relative w-auto pl-4 flex-initial">
                    <div class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full  bg-emerald-500">
                        <i class="fas fa-percent"></i>
                    </div>
                    </div>
                </div>
                <p class="text-sm text-blueGray-400 mt-4">
                    <span class="whitespace-nowrap"> — </span></p>
                </div>
            </div>
            </div>
        </div>
    </section>
@endsection --}}
