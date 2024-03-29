<?php

namespace App\Http\Controllers;

use App\Models\Skripsi;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;

class SkripsiController extends Controller
{
    public function index(Request $request)
    {
        $mahasiswa = Mahasiswa::leftJoin('users', 'mahasiswa.iduser', '=', 'users.id')
            ->leftJoin('dosen_wali', 'mahasiswa.nip', '=', 'dosen_wali.nip')
            ->where('mahasiswa.iduser', Auth::user()->id)
            ->select('mahasiswa.nama', 'mahasiswa.nim', 'mahasiswa.angkatan', 'mahasiswa.status', 'users.username', 'dosen_wali.nama as dosen_nama', 'mahasiswa.jalur_masuk')
            ->first();
        $nim = $request->user()->mahasiswa->nim;
        $skripsiData = Skripsi::where('nim',$nim);
        $semester = $request->input('semester_aktif');
        if ($semester) {
            $skripsiData->whereIn('semester_aktif', $semester);
        }

        $skripsiData = $skripsiData->select('nim', 'status', 'scanSkripsi', 'nilai', 'statusSkripsi', 'status', 'semester_aktif','lama_studi','tanggal_sidang')->get();
        return view('mahasiswa.skripsi', [
            'mahasiswa' => $mahasiswa,
            'skripsiData' => $skripsiData,
        ]);
    }

    public function create(Request $request)
    {
        $nim = $request->user()->mahasiswa->nim; // Use the logged-in user to get the nim
        $mahasiswa = Mahasiswa::leftJoin('dosen_wali', 'mahasiswa.nip', '=', 'dosen_wali.nip')
                            ->where('mahasiswa.nim', $nim)
                            ->select('mahasiswa.nama','mahasiswa.nim','mahasiswa.angkatan','dosen_wali.nama as dosen_nama', 'dosen_wali.nip')
                            ->first();
        // Periksa apakah data Skripsi sudah ada untuk semester yang dipilih
        $existingSkripsi = Skripsi::where('nim', $nim)->first();

        if ($existingSkripsi) {
            return redirect()->route('skripsi.index')->with('error', 'Anda telah memasukkan progress skripsi');
        }

        if ($mahasiswa) {
            // Get the active semesters for the given student
            $semesterAktifSkripsi = Skripsi::where('nim', $nim)->pluck('semester_aktif')->toArray();

            // Create an array of available semesters by diffing the full range and active semesters
            $availableSemesters = array_diff(range(7,14), $semesterAktifSkripsi);
        } else {
            // Handle the case where the Mahasiswa is not found
            return redirect()->route('skripsi.index')->with('error', 'Mahasiswa not found with the provided nim.');
        }
        
        return view('mahasiswa.skripsi-create', compact('availableSemesters', 'mahasiswa'));
    }

    public function store(Request $request): RedirectResponse
    {
        try{
            $validated = $request->validate([
                'semester_aktif' => ['required', 'numeric'], // Correct the validation rule syntax
                'nilai'=>[Rule::in(['A', 'B','C','D','E'])],
                'lama_studi'=>[Rule::in(['3', '4','5','6','7'])],
                'tanggal_sidang'=>['required'],
                'scanSkripsi' => ['required', 'file', 'mimes:pdf', 'max:10240'], // Correct the validation rule syntax
            ]);

            $PDFPath = null;

            if ($request->hasFile('scanSkripsi') && $request->file('scanSkripsi')->isValid()) {
                $PDFPath = $request->file('scanSkripsi')->store('file', 'public');
            }

            $skripsi = new Skripsi();
            $skripsi->semester_aktif = $validated['semester_aktif'];
            $skripsi->statusSkripsi = $request->input('statusSkripsi');
            $skripsi->nilai = $validated['nilai'];
            $skripsi->lama_studi = $validated['lama_studi'];
            $skripsi->tanggal_sidang = $validated['tanggal_sidang'];
            $skripsi->status = 'pending';
            $skripsi->scanSkripsi = $PDFPath; // Assign the PDF path here
            $skripsi->nim = $request->user()->mahasiswa->nim;
            $skripsi->nip = $request->user()->mahasiswa->nip;
            $saved = $skripsi->save();

            if ($saved) {
                return redirect()->route('skripsi.index')->with('success', 'Skripsi added successfully');
            } else {
                return redirect()->route('skripsi.create')->with('error', 'Failed to add Skripsi');
            }
        } catch (\Exception $e) {
            return redirect()->route('skripsi.create')->with('error', 'An error occurred while adding Skripsi: ' . $e->getMessage());
        }
    }

    private function update(Request $request, Skripsi $existingSkripsi): RedirectResponse
    {
        try{
            $validated = $request->validate([
                'nilai' => [Rule::in(['A', 'B', 'C', 'D', 'E'])],
                'lama_studi'=>[Rule::in(['3', '4','5','6','7'])],
                'tanggal_sidang'=>['required'],
                'scanSkripsi' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            ]);

            $PDFPath = null;

            if ($request->hasFile('scanSkripsi') && $request->file('scanSkripsi')->isValid()) {
                $PDFPath = $request->file('scanSkripsi')->store('file', 'public');
            }

            $existingSkripsi->statusSkripsi = $request->input('statusSkripsi');
            $existingSkripsi->nilai = $validated['nilai'];
            $existingSkripsi->lama_studi = $validated['lama_studi'];
            $existingSkripsi->tanggal_sidang = $validated['tanggal_sidang'];
            $existingSkripsi->status = 'pending';
            $existingSkripsi->scanSkripsi = $PDFPath; // Assign the PDF path here
            $saved = $existingSkripsi->save();

            if ($saved) {
                return redirect()->route('skripsi.index')->with('success', 'Skripsi updated successfully');
            } else {
                return redirect()->route('skripsi.create')->with('error', 'Failed to update Skripsi');
            }
        } catch (\Exception $e) {
            return redirect()->route('skripsi.create')->with('error', 'An error occurred while updating Skripsi: ' . $e->getMessage());
        }
    }

    public function getUpdateSkripsi(Request $request, $semester_aktif)
    {
        $user = $request->user();
        $nim = $request->user()->mahasiswa->nim;

        $mahasiswa = Mahasiswa::join('skripsi', 'mahasiswa.nim', 'skripsi.nim')
            ->where('semester_aktif', $semester_aktif)
            ->join('dosen_wali', 'mahasiswa.nip', 'dosen_wali.nip')
            ->select('skripsi.scanSkripsi', 'skripsi.statusSkripsi', 'skripsi.semester_aktif', 'skripsi.nilai', 'skripsi.tanggal_sidang', 'skripsi.lama_studi', 'mahasiswa.nama', 'mahasiswa.nim', 'mahasiswa.angkatan', 'dosen_wali.nama as dosen_nama')
            ->first();

        return view('mahasiswa.skripsi-update', ['user' => $user, 'mahasiswa' => $mahasiswa]);
    }

public function postUpdateSkripsi(Request $request, $semester_aktif)
{
    $user = $request->user();
    $nim = $request->user()->mahasiswa->nim;

    $validated = $request->validate([
        'scanSkripsi' => 'max:10240|file|mimes:pdf',
        'nilai' => 'nullable|string',
        'tanggal_sidang' => 'nullable|date',
        'lama_studi' => 'nullable|numeric',
    ]);

    DB::beginTransaction();

    try {
        if ($request->hasFile('scanSkripsi')) {
            $PDFPath = $request->file('scanSkripsi')->store('file', 'public');
            $validated['scanSkripsi'] = $PDFPath;

            Skripsi::where([
                'nim' => $nim,
                'semester_aktif' => $semester_aktif,
            ])->update([
                'scanSkripsi' => $validated['scanSkripsi'],
            ]);
        }

        if (!empty($validated['nilai'])) {
            Skripsi::where([
                'nim' => $nim,
                'semester_aktif' => $semester_aktif,
            ])->update([
                'nilai' => $validated['nilai'],
            ]);
        }
        
        if (!empty($validated['tanggal_sidang'])) {
            Skripsi::where([
                'nim' => $nim,
                'semester_aktif' => $semester_aktif,
            ])->update([
                'tanggal_sidang' => $validated['tanggal_sidang'],
            ]);
        }
        
        if (!empty($validated['lama_studi'])) {
            Skripsi::where([
                'nim' => $nim,
                'semester_aktif' => $semester_aktif,
            ])->update([
                'lama_studi' => $validated['lama_studi'],
            ]);
        }

        Skripsi::where([
            'nim' => $nim,
            'semester_aktif' => $semester_aktif,
        ])->update([
            'status' => 'pending',
        ]);

        DB::commit();

        return redirect()
            ->route('skripsi.index') // Assuming you have a route for Skripsi
            ->with('success', 'Data Skripsi berhasil diperbarui');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()
            ->route('skripsi.getSkripsi', ['semester_aktif' => $semester_aktif]) // Adjust this route as needed
            ->with('error', 'Gagal memperbarui Skripsi');
    }
}

}
