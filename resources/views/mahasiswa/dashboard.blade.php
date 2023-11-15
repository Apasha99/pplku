@extends('mahasiswa.layouts.layout2')

@section('content')
    <div class="mb-4 col-span-full xl:mb-2">
        <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Welcome, mahasiswa!</h1>
    </div>

    <div class="col-span-full xl:col-auto">
        <div
            class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm 2xl:col-span-2 dark:border-gray-700 sm:p-6 dark:bg-gray-800">
            <div class="items-center sm:flex xl:block 2xl:flex sm:space-x-4 xl:space-x-0 2xl:space-x-4">
                <img class="mb-4 rounded-lg w-28 h-28 sm:mb-0 xl:mb-4 2xl:mb-0"
                    src="https://flowbite-admin-dashboard.vercel.app/images/users/bonnie-green-2x.png" alt="Jese picture">
                <div>
                    <h3 class="mb-1 text-xl font-bold text-gray-900 dark:text-white">Maya Hart</h3>
                    <div class="mb-4 text-sm text-gray-500 dark:text-gray-400">
                        <p>24060121100037</p>
                        <p>INFORMATIKA</p>
                        <p>2021</p>
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
                                <svg class="w-6 h-6 dark:text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                    </path>
                                </svg>
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
                                    Johny Depp
                                </p>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection

@section('content2')
<div class="px-4 pb-4 grid w-full grid-cols-1 gap-4 mt-4 xl:grid-cols-2 2xl:grid-cols-3">
    <div
        class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:flex dark:border-gray-700 sm:p-6 dark:bg-gray-800">
        <div class="w-full">
            <h3 class="text-base font-bold text-gray-900 dark:text-gray-400">IRS</h3>
            <span class="text-2xl  leading-none text-gray-900 sm:text-3xl dark:text-white">24 SKS</span>
            <p class="flex items-center text-base font-normal text-gray-500 dark:text-gray-400">
                <span class="flex items-center mr-1.5 text-sm text-green-500 dark:text-green-400"><i class="fa-solid fa-check"></i> Aktif </span>
                pending
            </p>
        </div>
    </div>
    <div
        class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:flex dark:border-gray-700 sm:p-6 dark:bg-gray-800">
        <div class="w-full">
            <h3 class="text-base font-bold text-gray-900 dark:text-gray-400">KHS</h3>
            <span class="text-2xl  leading-none text-gray-900 sm:text-3xl dark:text-white">100 SKS</span>
            <p class="flex items-center text-base font-normal text-gray-500 dark:text-gray-400">
                <span class="flex items-center mr-1.5 text-sm text-red-500 dark:text-red-400"><i class="fas fa-arrow-down"></i> IPK 3.25 </span>
                pending
            </p>
        </div>
    </div>
    <div
        class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:flex dark:border-gray-700 sm:p-6 dark:bg-gray-800">
        <div class="w-full">
            <h3 class="text-base font-bold text-gray-900 dark:text-gray-400">PKL</h3>
            <span class="text-2xl  leading-none text-gray-900 sm:text-3xl dark:text-white">A</span>
            <p class="flex items-center text-base font-normal text-gray-500 dark:text-gray-400">
                <span class="flex items-center mr-1.5 text-sm text-green-500 dark:text-green-400"><i class="fa-solid fa-check"></i> Lulus </span>
                approved
            </p>
        </div>
    </div>
    <div
        class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:flex dark:border-gray-700 sm:p-6 dark:bg-gray-800">
        <div class="w-full">
            <h3 class="text-base font-bold text-gray-900 dark:text-gray-400">Skripsi</h3>
            <span class="text-2xl  leading-none text-gray-900 sm:text-3xl dark:text-white">—</span>
            <p class="flex items-center text-base font-normal text-gray-500 dark:text-gray-400">
                {{-- <span class="flex items-center mr-1.5 text-sm text-green-500 dark:text-green-400"> — </span> --}}
                —
            </p>
        </div>
    </div>
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
