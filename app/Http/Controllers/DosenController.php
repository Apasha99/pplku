<?php

namespace App\Http\Controllers;

use Dompdf\Dompdf;
use App\Models\IRS;
use App\Models\KHS;
use App\Models\PKL;
use Dompdf\Options;
use App\Models\Dosen;
use App\Models\Skripsi;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DosenController extends Controller
{
    public function detail(Request $request){
        $nip = $request->user()->dosen->nip;
        $dosens = Dosen::join('users', 'dosen_wali.iduser', '=', 'users.id')
            ->where('nip', $nip)
            ->select('dosen_wali.nama', 'dosen_wali.nip', 'users.id', 'users.username', 'users.foto')
            ->first();
        $mahasiswaPerwalian = Mahasiswa::join('dosen_wali','mahasiswa.nip','=','dosen_wali.nip')
                ->join('users', 'mahasiswa.iduser', '=', 'users.id')
                ->where('dosen_wali.iduser', Auth::user()->id)
                ->select('mahasiswa.nama', 'mahasiswa.nim', 'mahasiswa.angkatan', 'mahasiswa.status', 'dosen_wali.nip as dosen_wali_nip', 'users.foto')
                ->get();
        return view('doswal.perwalian', [
            'mahasiswaPerwalian' => $mahasiswaPerwalian,'dosens'=>$dosens
        ]);
    }

    public function dataMahasiswa(Request $request, $nim){
        $nip = $request->user()->dosen->nip;
        $dosens = Dosen::join('users', 'dosen_wali.iduser', '=', 'users.id')
            ->where('nip', $nip)
            ->select('dosen_wali.nama', 'dosen_wali.nip', 'users.id', 'users.username', 'users.foto')
            ->first();
        $mahasiswa =  Mahasiswa::join('dosen_wali','mahasiswa.nip','=','dosen_wali.nip')
            ->join('users', 'mahasiswa.iduser', '=', 'users.id')
            ->where('nim', $nim)
            ->where('dosen_wali.nip', $nip)
            ->where('dosen_wali.iduser', Auth::user()->id)
            ->select('mahasiswa.nama', 'mahasiswa.nim', 'mahasiswa.angkatan', 'mahasiswa.status', 'dosen_wali.nip as dosen_wali_nip', 'dosen_wali.nama as dosen_nama','users.foto')
            ->get();

        $irsData = IRS::join('mahasiswa','mahasiswa.nim','=','irs.nim')
            ->where('irs.nim', $nim)
            ->select('mahasiswa.status as mhsstatus','irs.status as status', 'irs.semester_aktif','irs.jumlah_sks','irs.scanIRS')
            ->get()
            ->keyBy('semester_aktif'); // Gunakan semester_aktif sebagai kunci array

        $khsData = KHS::join('mahasiswa','mahasiswa.nim','=','khs.nim')
            ->where('khs.nim', $nim)
            ->select('mahasiswa.status as mhsstatus','khs.status as status', 'khs.semester_aktif','khs.jumlah_sks','khs.jumlah_sks_kumulatif','khs.ip_semester','khs.ip_kumulatif')
            ->get()
            ->keyBy('semester_aktif');

        $pklData = PKL::join('mahasiswa','mahasiswa.nim','=','pkl.nim')
            ->where('pkl.nim', $nim)
            ->select('mahasiswa.status as mhsstatus','pkl.status as status', 'pkl.semester_aktif', 'pkl.nilai','pkl.scanPKL')
            ->get()
            ->keyBy('semester_aktif');
    
        $skripsiData = Skripsi::join('mahasiswa','mahasiswa.nim','=','skripsi.nim')
            ->where('skripsi.nim', $nim)
            ->select('mahasiswa.status as mhsstatus','skripsi.status as status', 'skripsi.semester_aktif', 'skripsi.nilai','skripsi.scanSkripsi','skripsi.lama_studi','skripsi.tanggal_sidang')
            ->get()
            ->keyBy('semester_aktif');

        $lastVerifiedPKL = PKL::join('mahasiswa','mahasiswa.nim','=','pkl.nim')
            ->where('pkl.nim', $nim)
            ->where('pkl.status', 'verified')
            ->select('mahasiswa.status as mhsstatus','pkl.status as status', 'pkl.semester_aktif', 'pkl.nilai','pkl.scanPKL')
            ->orderBy('semester_aktif')
            ->first();

        return view('doswal.details', [
            'mahasiswa' => $mahasiswa,'irsData'=>$irsData, 'khsData'=>$khsData, 'pklData'=>$pklData,'skripsiData'=>$skripsiData,'lastVerifiedPKL'=>$lastVerifiedPKL, 'dosens'=>$dosens
        ]);
    }

    public function searchMhs(Request $request)
    {
        $nip = $request->user()->dosen->nip;
        $search = $request->input('users-search');
        $dosens = Dosen::join('users', 'dosen_wali.iduser', '=', 'users.id')
            ->where('nip', $nip)
            ->select('dosen_wali.nama', 'dosen_wali.nip', 'users.id', 'users.username', 'users.foto')
            ->first();
        $mahasiswaPerwalian = Mahasiswa::join('dosen_wali','mahasiswa.nip','=','dosen_wali.nip')
                ->join('users', 'mahasiswa.iduser', '=', 'users.id')
                ->where('dosen_wali.iduser', Auth::user()->id)
                ->where(function ($query) use ($search) {
                    $query->where('mahasiswa.nama', 'like', '%' . $search . '%')
                        ->orWhere('mahasiswa.nim', 'like', '%' . $search . '%');
                })
                ->select('mahasiswa.nama', 'mahasiswa.nim', 'mahasiswa.angkatan', 'mahasiswa.status', 'dosen_wali.nip as dosen_wali_nip', 'users.foto')
                ->get();
        
        if ($mahasiswaPerwalian) {
            // Mahasiswa ditemukan, maka redirect ke 'perwalian' dengan membawa nim mahasiswa
            return view('doswal.perwalian', ['search' => $search,'mahasiswaPerwalian'=>$mahasiswaPerwalian,'dosens'=>$dosens]);
        } else {
            // Mahasiswa tidak ditemukan, maka redirect ke 'perwalian' dengan pesan kesalahan
            return redirect()->route('perwalian')->with('error', 'Tidak ada mahasiswa yang dicari di database');
        }
    }



    public function listPKL(Request $request){
        $nip = $request->user()->dosen->nip;
        $dosens = Dosen::join('users', 'dosen_wali.iduser', '=', 'users.id')
            ->where('nip', $nip)
            ->select('dosen_wali.nama', 'dosen_wali.nip', 'users.id', 'users.username', 'users.foto')
            ->first();
        $pkl = Dosen::join('users', 'dosen_wali.iduser', '=', 'users.id')
                ->join('mahasiswa','mahasiswa.nip','=','dosen_wali.nip')
                ->where('dosen_wali.nip',$nip)
                ->join('pkl','pkl.nim','=','mahasiswa.nim')
                ->select('mahasiswa.nama','mahasiswa.nim','mahasiswa.angkatan','pkl.semester_aktif','pkl.scanPKL','pkl.nilai','pkl.status','pkl.statusPKL')
                ->get();
        return view('listPKL', ['pkl'=>$pkl,'dosens'=>$dosens]);
    }

    public function listSkripsi(Request $request){
        $nip = $request->user()->dosen->nip;
        $dosens = Dosen::join('users', 'dosen_wali.iduser', '=', 'users.id')
            ->where('nip', $nip)
            ->select('dosen_wali.nama', 'dosen_wali.nip', 'users.id', 'users.username', 'users.foto')
            ->first();
        $skripsi = Dosen::join('users', 'dosen_wali.iduser', '=', 'users.id')
                ->join('mahasiswa','mahasiswa.nip','=','dosen_wali.nip')
                ->where('dosen_wali.nip',$nip)
                ->join('skripsi','skripsi.nim','=','mahasiswa.nim')
                ->select('mahasiswa.nama','mahasiswa.nim','mahasiswa.angkatan','skripsi.semester_aktif','skripsi.scanSkripsi','skripsi.nilai','skripsi.status','skripsi.statusSkripsi')
                ->get();
        return view('listSkripsi', ['skripsi'=>$skripsi,'dosens'=>$dosens]);
    }

    public function RekapPKL(Request $request){
        $nip = $request->user()->dosen->nip;
        $dosens = Dosen::join('users', 'dosen_wali.iduser', '=', 'users.id')
            ->where('nip', $nip)
            ->select('dosen_wali.nama', 'dosen_wali.nip', 'users.id', 'users.username', 'users.foto')
            ->first();
        $angkatan = [];
        $tahunSekarang = date('Y');
        
        // Inisialisasi array untuk menyimpan hasil akhir
        
        // Mengisi array $angkatan dengan rentang tahun dari tahun saat ini sampai 6 tahun ke belakang
        for ($i = 0; $i <= 6; $i++) {
            $angkatan[] = $tahunSekarang - $i;
        }
        $result = array_fill_keys($angkatan, ['pkl_lulus_count'=>0, 'pkl_tidak_lulus_count'=>0]);
        //dd($angkatan);
        $mahasiswas = DB::table('mahasiswa as m')
                ->leftJoin('dosen_wali' , 'dosen_wali.nip','=','m.nip')
                ->leftJoin('pkl as p', 'm.nim', '=', 'p.nim')
                ->whereIn('m.angkatan', $angkatan)
                ->where('dosen_wali.nip',$nip)
                ->select('m.angkatan', DB::raw('COALESCE(SUM(CASE WHEN p.status = "verified" THEN 1 ELSE 0 END), 0) as pkl_lulus_count'), 
                                        DB::raw('COALESCE(SUM(CASE WHEN p.nim IS NULL OR p.status != "verified" THEN 1 ELSE 0 END), 0) as pkl_tidak_lulus_count'))
                ->groupBy('m.angkatan')
                ->get()
                ->each(function ($item, $key) use (&$result) {
                    // Mengisi array $result dengan hasil query
                    $result[$item->angkatan]['pkl_lulus_count'] = $item->pkl_lulus_count;
                    $result[$item->angkatan]['pkl_tidak_lulus_count'] = $item->pkl_tidak_lulus_count;
                });
                
            //untuk rekap skripsi
        $result = collect($result);
    
        return view('doswal.rekappkl', ['result' => $result,'angkatan'=>$angkatan,'mahasiswas'=>$mahasiswas,'dosens'=>$dosens]);
    }

    public function DownloadRekapPKL(Request $request) {
        $nip = $request->user()->dosen->nip;
        $dosens = Dosen::join('users', 'dosen_wali.iduser', '=', 'users.id')
            ->where('nip', $nip)
            ->select('dosen_wali.nama', 'dosen_wali.nip', 'users.id', 'users.username', 'users.foto')
            ->first();
        $angkatan = [];
        $tahunSekarang = date('Y');
        // Inisialisasi array untuk menyimpan hasil akhir
        
        // Mengisi array $angkatan dengan rentang tahun dari tahun saat ini sampai 6 tahun ke belakang
        for ($i = 0; $i <= 6; $i++) {
            $angkatan[] = $tahunSekarang - $i;
        }

        $result = array_fill_keys($angkatan, ['pkl_lulus_count'=>0, 'pkl_tidak_lulus_count'=>0]);
            //dd($angkatan);
        $mahasiswas = DB::table('mahasiswa as m')
            ->leftJoin('dosen_wali' , 'dosen_wali.nip','=','m.nip')
            ->leftJoin('pkl as p', 'm.nim', '=', 'p.nim')
            ->whereIn('m.angkatan', $angkatan)
            ->where('dosen_wali.nip',$nip)
            ->select('m.angkatan', DB::raw('COALESCE(SUM(CASE WHEN p.status = "verified" THEN 1 ELSE 0 END), 0) as pkl_lulus_count'), 
                                    DB::raw('COALESCE(SUM(CASE WHEN p.nim IS NULL OR p.status != "verified" THEN 1 ELSE 0 END), 0) as pkl_tidak_lulus_count'))
            ->groupBy('m.angkatan')
            ->get()
            ->each(function ($item, $key) use (&$result) {
                // Mengisi array $result dengan hasil query
                $result[$item->angkatan]['pkl_lulus_count'] = $item->pkl_lulus_count;
                $result[$item->angkatan]['pkl_tidak_lulus_count'] = $item->pkl_tidak_lulus_count;
            });
        $result = collect($result);


        $pdf = app('dompdf.wrapper');
        $pdf ->loadView('doswal.DownloadRekapPKLDoswal',['mahasiswas'=>$mahasiswas, 'angkatan'=>$angkatan,'result'=>$result,'dosens'=>$dosens]);
        return $pdf->stream('rekap-pkl.pdf');
    }

    public function RekapSkripsi(Request $request){
        $nip = $request->user()->dosen->nip;
        $dosens = Dosen::join('users', 'dosen_wali.iduser', '=', 'users.id')
            ->where('nip', $nip)
            ->select('dosen_wali.nama', 'dosen_wali.nip', 'users.id', 'users.username', 'users.foto')
            ->first();
        $angkatan = [];
        $tahunSekarang = date('Y');
        
        // Inisialisasi array untuk menyimpan hasil akhir
        
        // Mengisi array $angkatan dengan rentang tahun dari tahun saat ini sampai 6 tahun ke belakang
        for ($i = 0; $i <= 6; $i++) {
            $angkatan[] = $tahunSekarang - $i;
        }
        $result = array_fill_keys($angkatan, ['lulus_count'=>0, 'tidak_lulus_count'=>0]);
        //dd($angkatan);
        $mahasiswasSkripsi = DB::table('mahasiswa as m')
                ->leftJoin('skripsi as s', 'm.nim', '=', 's.nim')
                ->leftJoin('dosen_wali', 'm.nip','=','dosen_wali.nip')
                ->whereIn('m.angkatan', $angkatan)
                ->where('dosen_wali.nip',$nip)
                ->select('m.angkatan', DB::raw('COALESCE(SUM(CASE WHEN s.status = "verified" THEN 1 ELSE 0 END), 0) as lulus_count'), 
                                        DB::raw('COALESCE(SUM(CASE WHEN s.nim IS NULL OR s.status != "verified" THEN 1 ELSE 0 END), 0) as tidak_lulus_count'))
                ->groupBy('m.angkatan')
                ->get()
                ->each(function ($item, $key) use (&$result) {
                    // Mengisi array $result dengan hasil query
                    $result[$item->angkatan]['lulus_count'] = $item->lulus_count;
                    $result[$item->angkatan]['tidak_lulus_count'] = $item->tidak_lulus_count;
                });
                
            //untuk rekap skripsi
        $result = collect($result);
        //dd($result);
        return view('doswal.rekapskripsi', ['result' => $result,'angkatan'=>$angkatan,'mahasiswasSkripsi'=>$mahasiswasSkripsi,'dosens'=>$dosens]);
    }
    
    public function DownloadRekapSkripsi(Request $request) {
        $nip = $request->user()->dosen->nip;
        $dosens = Dosen::join('users', 'dosen_wali.iduser', '=', 'users.id')
            ->where('nip', $nip)
            ->select('dosen_wali.nama', 'dosen_wali.nip', 'users.id', 'users.username', 'users.foto')
            ->first();
        $angkatan = [];
        
        $tahunSekarang = date('Y');
        // Inisialisasi array untuk menyimpan hasil akhir
        
        // Mengisi array $angkatan dengan rentang tahun dari tahun saat ini sampai 6 tahun ke belakang
        for ($i = 0; $i <= 6; $i++) {
            $angkatan[] = $tahunSekarang - $i;
        }
        $result = array_fill_keys($angkatan, ['lulus_count' => 0, 'tidak_lulus_count' => 0]);
            //dd($angkatan);
            //untuk rekap skripsi

        $mahasiswasSkripsi = DB::table('mahasiswa as m')
            ->leftJoin('skripsi as s', 'm.nim', '=', 's.nim')
            ->leftJoin('dosen_wali', 'm.nip','=','dosen_wali.nip')
            ->whereIn('m.angkatan', $angkatan)
            ->where('dosen_wali.nip',$nip)
            ->select('m.angkatan', 
                     DB::raw('COALESCE(SUM(CASE WHEN s.status = "verified" THEN 1 ELSE 0 END), 0) as lulus_count'), 
                     DB::raw('COALESCE(SUM(CASE WHEN s.nim IS NULL OR s.status != "verified" THEN 1 ELSE 0 END), 0) as tidak_lulus_count'),
                     's.tanggal_sidang', // menambahkan tanggal_sidang
                     's.lama_studi' // menambahkan lama_studi
                    )
            ->groupBy('m.angkatan')
            ->get()
            ->each(function ($item, $key) use (&$result) {
                // Mengisi array $result dengan hasil query
                $result[$item->angkatan]['lulus_count'] = $item->lulus_count;
                $result[$item->angkatan]['tidak_lulus_count'] = $item->tidak_lulus_count;
                $result[$item->angkatan]['tanggal_sidang'] = $item->tanggal_sidang; // menambahkan tanggal_sidang
                $result[$item->angkatan]['lama_studi'] = $item->lama_studi; // menambahkan lama_studi
            });

            // Mengubah $result menjadi koleksi Laravel
        $result = collect($result);
        
        $pdf = app('dompdf.wrapper');
        $pdf ->loadView('doswal.DownloadRekapSkripsiDoswal',['mahasiswasSkripsi'=>$mahasiswasSkripsi, 'angkatan'=>$angkatan,'result'=>$result,'dosens'=>$dosens]);
        return $pdf->stream('rekap-skripsi.pdf');
    }
    
    public function edit(Request $request)
    {
        $user = $request->user();
        $nip = $request->user()->dosen->nip;
        $dosens = Dosen::join('users', 'dosen_wali.iduser', '=', 'users.id')
            ->where('nip', $nip)
            ->select('dosen_wali.nama', 'dosen_wali.nip', 'users.id', 'users.username', 'users.foto')
            ->first();
        return view('doswal.profil', ['user' => $user, 'dosens' => $dosens]);
    }

    public function showEdit(Request $request)
    {
        $user = $request->user();
        $nip = $request->user()->dosen->nip;
        $dosens = Dosen::join('users', 'dosen_wali.iduser', '=', 'users.id')
            ->where('nip', $nip)
            ->select('dosen_wali.nama', 'dosen_wali.nip', 'users.id', 'users.username', 'users.password', 'users.foto')
            ->first();
        return view('doswal.profil-edit', ['user' => $user, 'dosens' => $dosens]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'username' => 'nullable|string',
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:8',
            'new_confirm_password' => 'nullable|same:new_password',
            'foto' => 'max:10240|image|mimes:jpeg,png,jpg',
        ]);

        if ($request->has('foto')) {
            $fotoPath = $request->file('foto')->store('profile', 'public');
            $validated['foto'] = $fotoPath;

            $user->update([
                'foto' => $validated['foto'],
            ]);
        }

        // Check if 'new_password' key exists and not null in $validated
        if (array_key_exists('new_password', $validated) && $validated['new_password'] !== null) {
            if (!Hash::check($validated['current_password'], $user->password)) {
                return redirect()
                    ->route('doswal.showEdit4')
                    ->with('error', 'Password lama tidak cocok.');
            }
        }

        DB::beginTransaction();

        try {
            $userData = ['username' => $validated['username'] ?? null];

            if (!is_null($userData['username'])) {
                $user->update($userData);

                Dosen::where('iduser', $user->id)->update($userData);
            }

            if (array_key_exists('new_password', $validated) && $validated['new_password'] !== null) {
                $user->update([
                    'password' => Hash::make($validated['new_password']),
                ]);
            }

            DB::commit();

            return redirect()
                ->route('edit4')
                ->with('success', 'Profil berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());  // Add this line
            return redirect()
                ->route('showEdit4')
                ->with('error', 'Gagal memperbarui profil.');
        }
        
    }

    public function lulusPKL(Request $request, $angkatan, $status) {
        $nip = $request->user()->dosen->nip;
        //dd($nip);
        $dosens = Dosen::join('users', 'dosen_wali.iduser', '=', 'users.id')
            ->where('nip', $nip)
            ->select('dosen_wali.nama', 'dosen_wali.nip', 'users.id', 'users.username', 'users.foto')
            ->first();
        $doswal = Dosen::leftJoin('users', 'dosen_wali.iduser', '=', 'users.id')
                ->where('dosen_wali.iduser', Auth::user()->id)
                ->select('dosen_wali.nama', 'dosen_wali.nip', 'users.username')
                ->first();
        //dd($doswal);
        $mahasiswas = Mahasiswa::leftJoin('dosen_wali' , 'dosen_wali.nip','=','mahasiswa.nip')
                                ->leftJoin('pkl', function ($join) use ($status) {
                                    $join->on('mahasiswa.nim', '=', 'pkl.nim')
                                        ->where('pkl.status', '=', 'verified');
                                })
                                ->where('mahasiswa.angkatan', $angkatan)
                                ->where('dosen_wali.nip',$nip)
                                ->where(function ($query) use ($status) {
                                    $query->where('pkl.status', $status);
                                })
                                ->select('mahasiswa.nama', 'mahasiswa.nim', 'mahasiswa.angkatan', 'pkl.nilai', 'pkl.statusPKL', 'pkl.status','mahasiswa.nip')
                                ->get();
        //dd($mahasiswas);
    
        return view('doswal.luluspkldoswal', ['mahasiswas' => $mahasiswas->isEmpty() ? [] : $mahasiswas, 'doswal'=>$doswal,'dosens'=>$dosens,'angkatan'=>$angkatan,'status'=>$status]);
    }    

    public function tidaklulusPKL(Request $request, $angkatan, $status) {
        $nip = $request->user()->dosen->nip;
        $dosens = Dosen::join('users', 'dosen_wali.iduser', '=', 'users.id')
            ->where('nip', $nip)
            ->select('dosen_wali.nama', 'dosen_wali.nip', 'users.id', 'users.username', 'users.foto')
            ->first();
        $doswal = Dosen::leftJoin('users', 'dosen_wali.iduser', '=', 'users.id')
                ->where('dosen_wali.iduser', Auth::user()->id)
                ->select('dosen_wali.nama', 'dosen_wali.nip', 'users.username')
                ->first();
        $mahasiswas = Mahasiswa::leftJoin('pkl', function ($join) use ($status) {
                    $join->on('mahasiswa.nim', '=', 'pkl.nim')
                        ->where('pkl.status', '=', 'verified');
                    })
                    ->leftJoin('dosen_wali' , 'dosen_wali.nip','=','mahasiswa.nip')
                    ->where('mahasiswa.angkatan', $angkatan)
                    ->where('dosen_wali.nip',$nip)
                    ->where(function ($query) use ($status) {
                        $query->whereNull('pkl.nim')
                            ->orWhere(function ($query) use ($status) {
                                $query->where('pkl.status', '=', $status);
                            });
                    })
                    ->select('mahasiswa.nama', 'mahasiswa.nim', 'mahasiswa.angkatan', 'pkl.nilai', 'pkl.status','dosen_wali.nip')
                    ->get();

    
        return view('doswal.tidakluluspkldoswal', ['mahasiswas' => $mahasiswas->isEmpty() ? [] : $mahasiswas, 'doswal'=>$doswal,'dosens'=>$dosens,'angkatan'=>$angkatan,'status'=>$status]);
    }   
    
    public function lulusSkripsi(Request $request, $angkatan, $status){
        $nip = $request->user()->dosen->nip;
        $dosens = Dosen::join('users', 'dosen_wali.iduser', '=', 'users.id')
            ->where('nip', $nip)
            ->select('dosen_wali.nama', 'dosen_wali.nip', 'users.id', 'users.username', 'users.foto')
            ->first();
        $doswal = Dosen::leftJoin('users', 'dosen_wali.iduser', '=', 'users.id')
                ->where('dosen_wali.iduser', Auth::user()->id)
                ->select('dosen_wali.nama', 'dosen_wali.nip', 'users.username')
                ->first();
        $mahasiswas = Mahasiswa::leftJoin('skripsi', function ($join) use ($status) {
            $join->on('mahasiswa.nim', '=', 'skripsi.nim')
                ->where('skripsi.status', '=', 'verified');
        })
        ->leftJoin('dosen_wali' , 'dosen_wali.nip','=','mahasiswa.nip')
        ->where('mahasiswa.angkatan', $angkatan)
        ->where('dosen_wali.nip',$nip)
        ->where(function ($query) use ($status) {
            $query->where('skripsi.status', $status);
        })
        ->select('mahasiswa.nama', 'mahasiswa.nim', 'mahasiswa.angkatan', 'skripsi.nilai', 'skripsi.status','skripsi.tanggal_sidang','skripsi.lama_studi','mahasiswa.nip')
        ->get();
    
        return view('doswal.lulusskripsidoswal', ['mahasiswas' => $mahasiswas, 'doswal'=>$doswal,'dosens'=>$dosens,'angkatan'=>$angkatan,'status'=>$status]);
    }   

    public function tidaklulusSkripsi(Request $request, $angkatan, $status){
        $nip = $request->user()->dosen->nip;
        $dosens = Dosen::join('users', 'dosen_wali.iduser', '=', 'users.id')
            ->where('nip', $nip)
            ->select('dosen_wali.nama', 'dosen_wali.nip', 'users.id', 'users.username', 'users.foto')
            ->first();
        $doswal = Dosen::leftJoin('users', 'dosen_wali.iduser', '=', 'users.id')
                ->where('dosen_wali.iduser', Auth::user()->id)
                ->select('dosen_wali.nama', 'dosen_wali.nip', 'users.username')
                ->first();
        $mahasiswas = Mahasiswa::leftJoin('skripsi', function ($join) use ($status, $nip) {
                    $join->on('mahasiswa.nim', '=', 'skripsi.nim')
                        ->where('skripsi.status', '=', 'verified');
                    })
                    ->leftJoin('dosen_wali', 'mahasiswa.nip', '=', 'dosen_wali.nip')
                    ->where('mahasiswa.angkatan', $angkatan)
                    ->where('dosen_wali.nip', $nip)
                    ->where(function ($query) use ($status) {
                        $query->whereNull('skripsi.nim')
                            ->orWhere(function ($query) use ($status) {
                                $query->where('skripsi.status', '=', $status);
                            });
                    })
                    ->select('mahasiswa.nama', 'mahasiswa.nim', 'mahasiswa.angkatan', 'skripsi.nilai', 'skripsi.status','mahasiswa.nip')
                    ->get();
    
        return view('doswal.tidaklulusskripsidoswal', ['mahasiswas' => $mahasiswas, 'doswal'=>$doswal,'angkatan'=>$angkatan,'nip'=>$nip,'dosens'=>$dosens,'angkatan'=>$angkatan,'status'=>$status]);
    }    

    public function DoswalListPKLLulus(Request $request, $angkatan, $status) {
        $nip = $request->user()->dosen->nip;
        $dosens = Dosen::join('users', 'dosen_wali.iduser', '=', 'users.id')
            ->where('nip', $nip)
            ->select('dosen_wali.nama', 'dosen_wali.nip', 'users.id', 'users.username', 'users.foto')
            ->first();
        $mahasiswas = Mahasiswa::leftJoin('pkl', function ($join) use ($status) {
                                $join->on('mahasiswa.nim', '=', 'pkl.nim')
                                    ->where('pkl.status', '=', 'verified');
                            })
                            ->leftJoin('dosen_wali' , 'dosen_wali.nip','=','mahasiswa.nip')
                            ->where('mahasiswa.angkatan', $angkatan)
                            ->where('dosen_wali.nip', $nip)
                            ->where(function ($query) use ($status) {
                                $query->where('pkl.status', $status);
                            })
                            ->select('mahasiswa.nama', 'mahasiswa.nim', 'mahasiswa.angkatan', 'pkl.nilai', 'pkl.statusPKL', 'pkl.status')
                            ->get();
        $pdf = app('dompdf.wrapper');
        $pdf ->loadView('doswal.downloadlistlulusPKL',['mahasiswas'=>$mahasiswas, 'dosens'=>$dosens,'angkatan'=>$angkatan,'status'=>$status]);
        return $pdf->stream('daftar-list-pkl-lulus.pdf');
    
        if ($mahasiswas->isEmpty()) {
            // Lakukan penanganan jika $mahasiswas kosong, seperti menampilkan pesan atau mengarahkan ke halaman lain
            return redirect()->back()->with('error', 'Tidak ada data yang tersedia.');
        }
    }
    
    public function DoswalListPKLBelum(Request $request, $angkatan, $status){
        $nip = $request->user()->dosen->nip;
        $dosens = Dosen::join('users', 'dosen_wali.iduser', '=', 'users.id')
            ->where('nip', $nip)
            ->select('dosen_wali.nama', 'dosen_wali.nip', 'users.id', 'users.username', 'users.foto')
            ->first();
        $mahasiswas = Mahasiswa::leftJoin('pkl', function ($join) use ($status) {
                                $join->on('mahasiswa.nim', '=', 'pkl.nim')
                                    ->where('pkl.status', '=', 'verified');
                                })
                                ->leftJoin('dosen_wali', 'mahasiswa.nip', '=', 'dosen_wali.nip')
                                ->where('mahasiswa.angkatan', $angkatan)
                                ->where('dosen_wali.nip',$nip)
                                ->where(function ($query) use ($status) {
                                    $query->whereNull('pkl.nim')
                                        ->orWhere(function ($query) use ($status) {
                                            $query->where('pkl.status', '=', $status);
                                        });
                                })
                                ->select('mahasiswa.nama', 'mahasiswa.nim', 'mahasiswa.angkatan', 'pkl.nilai', 'pkl.status')
                                ->get();
        $pdf = app('dompdf.wrapper');
        $pdf ->loadView('doswal.downloadlisttidaklulusPKL',['mahasiswas'=>$mahasiswas, 'dosens'=>$dosens,'angkatan'=>$angkatan,'status'=>$status]);
        return $pdf->stream('daftar-list-pkl-tidak-lulus.pdf');
    }

    public function DoswalListSkripsiLulus(Request $request, $angkatan, $status) {
        $nip = $request->user()->dosen->nip;
        $dosens = Dosen::join('users', 'dosen_wali.iduser', '=', 'users.id')
            ->where('nip', $nip)
            ->select('dosen_wali.nama', 'dosen_wali.nip', 'users.id', 'users.username', 'users.foto')
            ->first();
        $mahasiswas = Mahasiswa::leftJoin('skripsi', function ($join) use ($status) {
                                $join->on('mahasiswa.nim', '=', 'skripsi.nim')
                                    ->where('skripsi.status', '=', 'verified');
                            })
                            ->leftJoin('dosen_wali', 'mahasiswa.nip', '=', 'dosen_wali.nip')
                            ->where('mahasiswa.angkatan', $angkatan)
                            ->where('dosen_wali.nip',$nip)
                            ->where(function ($query) use ($status) {
                                $query->where('skripsi.status', $status);
                            })
                            ->select('mahasiswa.nama', 'mahasiswa.nim', 'mahasiswa.angkatan', 'skripsi.nilai', 'skripsi.statusSkripsi', 'skripsi.status','skripsi.tanggal_sidang','skripsi.lama_studi')
                            ->get();
    
        if ($mahasiswas->isEmpty()) {
            // Lakukan penanganan jika $mahasiswas kosong, seperti menampilkan pesan atau mengarahkan ke halaman lain
            return redirect()->back()->with('error', 'Tidak ada data yang tersedia.');
        }

        $pdf = app('dompdf.wrapper');
        $pdf ->loadView('doswal.downloadlistlulusSkripsi',['mahasiswas'=>$mahasiswas, 'dosens'=>$dosens,'angkatan'=>$angkatan,'status'=>$status]);
        return $pdf->stream('daftar-list-skripsi-lulus.pdf');
    }
    
    public function DoswalListSkripsiBelum(Request $request, $angkatan, $status){
        $nip = $request->user()->dosen->nip;
        $dosens = Dosen::join('users', 'dosen_wali.iduser', '=', 'users.id')
            ->where('nip', $nip)
            ->select('dosen_wali.nama', 'dosen_wali.nip', 'users.id', 'users.username', 'users.foto')
            ->first();
        $mahasiswas = Mahasiswa::leftJoin('skripsi', function ($join) use ($status) {
                                $join->on('mahasiswa.nim', '=', 'skripsi.nim')
                                    ->where('skripsi.status', '=', 'verified');
                                })
                                ->leftJoin('dosen_wali', 'mahasiswa.nip', '=', 'dosen_wali.nip')
                                ->where('mahasiswa.angkatan', $angkatan)
                                ->where('dosen_wali.nip',$nip)
                                ->where(function ($query) use ($status) {
                                    $query->whereNull('skripsi.nim')
                                        ->orWhere(function ($query) use ($status) {
                                            $query->where('skripsi.status', '=', $status);
                                        });
                                })
                                ->select('mahasiswa.nama', 'mahasiswa.nim', 'mahasiswa.angkatan', 'skripsi.nilai', 'skripsi.status', 'skripsi.tanggal_sidang','skripsi.lama_studi')
                                ->get();
        $pdf = app('dompdf.wrapper');
        $pdf ->loadView('doswal.downloadlisttidaklulusSkripsi',['mahasiswas'=>$mahasiswas, 'angkatan'=>$angkatan,'status'=>$status,'dosens'=>$dosens]);
        return $pdf->stream('daftar-list-skripsi-tidak-lulus.pdf');
    }

    public function DoswalPreviewPKL(Request $request){
        $nip = $request->user()->dosen->nip;
        $dosens = Dosen::join('users', 'dosen_wali.iduser', '=', 'users.id')
            ->where('nip', $nip)
            ->select('dosen_wali.nama', 'dosen_wali.nip', 'users.id', 'users.username', 'users.foto')
            ->first();
        $angkatan = [];
        $tahunSekarang = date('Y');
        // Inisialisasi array untuk menyimpan hasil akhir
        
        // Mengisi array $angkatan dengan rentang tahun dari tahun saat ini sampai 6 tahun ke belakang
        for ($i = 0; $i <= 6; $i++) {
            $angkatan[] = $tahunSekarang - $i;
        }

        $result = array_fill_keys($angkatan, ['pkl_lulus_count'=>0, 'pkl_tidak_lulus_count'=>0]);
            //dd($angkatan);
        $mahasiswas = DB::table('mahasiswa as m')
                ->leftJoin('pkl as p', 'm.nim', '=', 'p.nim')
                ->leftJoin('dosen_wali', 'm.nip', '=', 'dosen_wali.nip')
                ->whereIn('m.angkatan', $angkatan)
                ->where('dosen_wali.nip',$nip)
                ->select('m.angkatan', DB::raw('COALESCE(SUM(CASE WHEN p.status = "verified" THEN 1 ELSE 0 END), 0) as pkl_lulus_count'), 
                                        DB::raw('COALESCE(SUM(CASE WHEN p.nim IS NULL OR p.status != "verified" THEN 1 ELSE 0 END), 0) as pkl_tidak_lulus_count'))
                ->groupBy('m.angkatan')
                ->get()
                ->each(function ($item, $key) use (&$result) {
                    // Mengisi array $result dengan hasil query
                    $result[$item->angkatan]['pkl_lulus_count'] = $item->pkl_lulus_count;
                    $result[$item->angkatan]['pkl_tidak_lulus_count'] = $item->pkl_tidak_lulus_count;
                });
        $result = collect($result);


        $pdf = app('dompdf.wrapper');
        $pdf ->loadView('doswal.DownloadRekapPKLDoswal',['mahasiswas'=>$mahasiswas, 'angkatan'=>$angkatan,'result'=>$result,'dosens'=>$dosens]);
        return $pdf->stream('rekap-pkl.pdf');
    }

    public function DoswalPreviewSkripsi(Request $request){
        $nip = $request->user()->dosen->nip;
        $dosens = Dosen::join('users', 'dosen_wali.iduser', '=', 'users.id')
            ->where('nip', $nip)
            ->select('dosen_wali.nama', 'dosen_wali.nip', 'users.id', 'users.username', 'users.foto')
            ->first();
        $angkatan = [];
        
        $tahunSekarang = date('Y');
        // Inisialisasi array untuk menyimpan hasil akhir
        
        // Mengisi array $angkatan dengan rentang tahun dari tahun saat ini sampai 6 tahun ke belakang
        for ($i = 0; $i <= 6; $i++) {
            $angkatan[] = $tahunSekarang - $i;
        }
        $result = array_fill_keys($angkatan, ['lulus_count' => 0, 'tidak_lulus_count' => 0]);
            //dd($angkatan);
            //untuk rekap skripsi

        $mahasiswasSkripsi = DB::table('mahasiswa as m')
            ->leftJoin('skripsi as s', 'm.nim', '=', 's.nim')
            ->leftJoin('dosen_wali', 'm.nip', '=', 'dosen_wali.nip')
            ->whereIn('m.angkatan', $angkatan)
            ->where('dosen_wali.nip',$nip)
            ->select('m.angkatan', 
                     DB::raw('COALESCE(SUM(CASE WHEN s.status = "verified" THEN 1 ELSE 0 END), 0) as lulus_count'), 
                     DB::raw('COALESCE(SUM(CASE WHEN s.nim IS NULL OR s.status != "verified" THEN 1 ELSE 0 END), 0) as tidak_lulus_count'),
                    )
            ->groupBy('m.angkatan')
            ->get()
            ->each(function ($item, $key) use (&$result) {
                // Mengisi array $result dengan hasil query
                $result[$item->angkatan]['lulus_count'] = $item->lulus_count;
                $result[$item->angkatan]['tidak_lulus_count'] = $item->tidak_lulus_count;
            });

            // Mengubah $result menjadi koleksi Laravel
        $result = collect($result);
        
        $pdf = app('dompdf.wrapper');
        $pdf ->loadView('doswal.DownloadRekapSkripsiDoswal',['mahasiswasSkripsi'=>$mahasiswasSkripsi, 'angkatan'=>$angkatan,'result'=>$result,'dosens'=>$dosens]);
        return $pdf->stream('rekap-skripsi.pdf');
    
    }

    public function RekapStatus(Request $request){
        $nip = $request->user()->dosen->nip;
        $dosens = Dosen::join('users', 'dosen_wali.iduser', '=', 'users.id')
            ->where('nip', $nip)
            ->select('dosen_wali.nama', 'dosen_wali.nip', 'users.id', 'users.username', 'users.foto')
            ->first();
        $angkatan = [];
        $tahunSekarang = date('Y');
        
        // Inisialisasi array untuk menyimpan hasil akhir
        
        // Mengisi array $angkatan dengan rentang tahun dari tahun saat ini sampai 6 tahun ke belakang
        for ($i = 0; $i <= 6; $i++) {
            $angkatan[] = $tahunSekarang - $i;
        }
        $result = array_fill_keys($angkatan, ['lulus_count' => 0, 'tidak_lulus_count' => 0, 'pkl_lulus_count' => 0, 'pkl_tidak_lulus_count' => 0, 
        'active' => 0,
        'cuti' => 0,
        'mangkir' => 0,
        'do' => 0,
        'lulus' => 0,
        'undur_diri' => 0,
        'meninggal_dunia'=> 0]);
        //dd($angkatan);
        $statusMahasiswa = Mahasiswa::leftJoin('dosen_wali', 'mahasiswa.nip', '=', 'dosen_wali.nip')
                    ->whereIn('mahasiswa.angkatan', $angkatan)
                    ->where('mahasiswa.nip',$nip)
                    ->whereIn('status', ['mangkir', 'undur_diri','active','do','meninggal_dunia','lulus','cuti'])
                    ->select('angkatan', 'status', DB::raw('COALESCE(COUNT(*), 0) as count'))
                    ->groupBy('angkatan', 'status')
                    ->get()
                    ->each(function ($item, $key) use (&$result) {
                        // Mengisi array $result dengan hasil query
                        $result[$item->angkatan][$item->status] = $item->count;
                });
            //untuk rekap skripsi
        $result = collect($result);
    
        return view('doswal.rekapstatus', ['result' => $result,'angkatan'=>$angkatan,'dosens'=>$dosens]);
    }

    public function DoswalPreviewStatus(Request $request){
        $nip = $request->user()->dosen->nip;
        $dosens = Dosen::join('users', 'dosen_wali.iduser', '=', 'users.id')
            ->where('nip', $nip)
            ->select('dosen_wali.nama', 'dosen_wali.nip', 'users.id', 'users.username', 'users.foto')
            ->first();
        $angkatan = [];
        $tahunSekarang = date('Y');
        for ($i = 0; $i <= 6; $i++) {
            $angkatan[] = $tahunSekarang - $i;
        }
        $result = array_fill_keys($angkatan, ['lulus_count' => 0, 'tidak_lulus_count' => 0, 'pkl_lulus_count' => 0, 'pkl_tidak_lulus_count' => 0, 
                'active' => 0,
                'cuti' => 0,
                'mangkir' => 0,
                'do' => 0,
                'lulus' => 0,
                'undur_diri' => 0,
                'meninggal_dunia'=> 0]);
        $statusMahasiswa = Mahasiswa::leftJoin('dosen_wali', 'mahasiswa.nip', '=', 'dosen_wali.nip')
                ->where('mahasiswa.nip',$nip)
                ->whereIn('angkatan', $angkatan)
                ->select('angkatan', 'status', DB::raw('COALESCE(COUNT(*), 0) as count'))
                ->groupBy('angkatan', 'status')
                ->get()
                ->each(function ($item, $key) use (&$result) {
                    // Mengisi array $result dengan hasil query
                    $result[$item->angkatan][$item->status] = $item->count;
                });
        $result = collect($result);

        $pdf = app('dompdf.wrapper');
        $pdf ->loadView('doswal.downloadrekapstatus',['angkatan'=>$angkatan,'result'=>$result,'dosens'=>$dosens]);
        return $pdf->stream('rekap-status.pdf');
    }

    public function daftarstatus(Request $request, $angkatan, $status){
        $nip = $request->user()->dosen->nip;
        $dosens = Dosen::join('users', 'dosen_wali.iduser', '=', 'users.id')
            ->where('nip', $nip)
            ->select('dosen_wali.nama', 'dosen_wali.nip', 'users.id', 'users.username', 'users.foto')
            ->first();
        $daftar = Mahasiswa::join('dosen_wali','dosen_wali.nip','=','mahasiswa.nip')
                            ->select('mahasiswa.nama','mahasiswa.nim','mahasiswa.angkatan','mahasiswa.status','dosen_wali.nama as dosen_nama')
                            ->where('mahasiswa.nip',$nip)
                            ->where('mahasiswa.angkatan',$angkatan)
                            ->where('mahasiswa.status',$status)
                            ->get();
    
        $namastatus = [
            'active' => 'Aktif',
            'lulus' => 'Lulus',
            'meninggal_dunia' => 'Meninggal Dunia',
            'do' => 'Drop Out',
            'cuti' => 'Cuti',
            'undur_diri' => 'Undur Diri',
            'mangkir' => 'Mangkir'
        ];
    
        $status_label = isset($namastatus[$status]) ? $namastatus[$status] : $status;
    
        return view('doswal.daftarstatus',['daftar'=>$daftar,'namastatus'=>$status_label,'angkatan'=>$angkatan,'status'=>$status,'dosens'=>$dosens]);
    }

    public function PreviewListStatus(Request $request, $angkatan, $status){
    
        $nip = $request->user()->dosen->nip;
        $dosens = Dosen::join('users', 'dosen_wali.iduser', '=', 'users.id')
            ->where('nip', $nip)
            ->select('dosen_wali.nama', 'dosen_wali.nip', 'users.id', 'users.username', 'users.foto')
            ->first();
        $daftar = Mahasiswa::join('dosen_wali','dosen_wali.nip','=','mahasiswa.nip')
                            ->select('mahasiswa.nama','mahasiswa.nim','mahasiswa.angkatan','mahasiswa.status','dosen_wali.nama as dosen_nama')
                            ->where('mahasiswa.nip',$nip)
                            ->where('mahasiswa.angkatan',$angkatan)
                            ->where('mahasiswa.status',$status)
                            ->get();

        $namastatus = [
            'active' => 'Aktif',
            'lulus' => 'Lulus',
            'meninggal_dunia' => 'Meninggal Dunia',
            'do' => 'Drop Out',
            'cuti' => 'Cuti',
            'undur_diri' => 'Undur Diri',
            'mangkir' => 'Mangkir'
        ];

        $status_label = isset($namastatus[$status]) ? $namastatus[$status] : $status;
        $pdf = app('dompdf.wrapper');
        $pdf ->loadView('doswal.downloadliststatus',['daftar'=>$daftar, 'namastatus'=>$status_label,'dosens'=>$dosens,'angkatan'=>$angkatan,'status'=>$status]);
        return $pdf->stream('daftar-list-status.pdf');
    }



}
