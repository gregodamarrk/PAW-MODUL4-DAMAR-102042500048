<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\MataKuliah;
use App\Models\Material;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Dosen Pengampu Default
        $dosen = User::updateOrCreate(
            ['email' => 'dosen@celoe.test'],
            [
                'name' => 'Dr. Ir. Budi Santoso, M.T.',
                'password' => Hash::make('dosen123'),
                'role' => 'dosen',
                'nomor_induk' => '198501012015041001',
            ]
        );

        // 2. Akun Mahasiswa Default
        $mahasiswa1 = User::updateOrCreate(
            ['email' => 'mahasiswa@celoe.test'],
            [
                'name' => 'Ahmad Pratama',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'mahasiswa',
                'nomor_induk' => '1301210001',
            ]
        );

        // 3. Mata Kuliah Dasar (Modul 3)
        $course1 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF301'],
            [
                'nama_mk' => 'Pengembangan Aplikasi Website',
                'deskripsi' => 'Mata kuliah yang membahas arsitektur aplikasi web modern menggunakan framework Laravel, MVC, Eloquent ORM, dan keamanan web.',
                'sks' => 4,
                'semester' => 'Ganjil 2026/2027',
                'dosen_id' => $dosen->id,
            ]
        );

        // 4. Daftarkan Mahasiswa ke Mata Kuliah Pertama (status approved)
        $course1->students()->syncWithoutDetaching([
            $mahasiswa1->id => ['status' => 'approved'],
        ]);

        // 5. Tambahkan Materi Contoh
        Material::updateOrCreate(
            ['course_id' => $course1->id, 'minggu_ke' => 1, 'urutan' => 1],
            [
                'judul' => 'Pengenalan Arsitektur MVC & Routing di Laravel 12',
                'konten' => 'Mempelajari alur HTTP Request, Route Middleware, Controller, dan pembuatan View menggunakan Blade Template.',
            ]
        );

        // TODO 8: Tambahkan minimal 2 mata kuliah lagi dengan semester berbeda, 1 mahasiswa tambahan, 2 tugas, dan contoh data submission

        // a. 1 Mahasiswa tambahan
        $mahasiswa2 = User::updateOrCreate(
            ['email' => 'mahasiswa2@celoe.test'],
            [
                'name' => 'Siti Rahmawati',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'mahasiswa',
                'nomor_induk' => '1301210002',
            ]
        );

        // b. 2 Mata kuliah tambahan dengan semester berbeda
        $course2 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'SI204'],
            [
                'nama_mk' => 'Analisis dan Perancangan Sistem Informasi',
                'deskripsi' => 'Membahas pemodelan proses bisnis, perancangan kebutuhan sistem, diagram UML, dan desain antarmuka pengguna.',
                'sks' => 3,
                'semester' => 'Genap 2025/2026',
                'dosen_id' => $dosen->id,
            ]
        );

        $course3 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF402'],
            [
                'nama_mk' => 'Keamanan dan Pengujian Perangkat Lunak',
                'deskripsi' => 'Membahas kerentanan aplikasi web, autentikasi, otorisasi, serta pengujian unit dan integrasi.',
                'sks' => 3,
                'semester' => 'Ganjil 2025/2026',
                'dosen_id' => $dosen->id,
            ]
        );

        // Daftarkan mahasiswa ke mata kuliah tambahan
        $course1->students()->syncWithoutDetaching([
            $mahasiswa2->id => ['status' => 'approved'],
        ]);
        $course2->students()->syncWithoutDetaching([
            $mahasiswa1->id => ['status' => 'approved'],
            $mahasiswa2->id => ['status' => 'pending'],
        ]);
        $course3->students()->syncWithoutDetaching([
            $mahasiswa1->id => ['status' => 'approved'],
        ]);

        // c. 2 Tugas (Assignment)
        $assignment1 = Assignment::updateOrCreate(
            [
                'course_id' => $course1->id,
                'judul' => 'Tugas Pendahuluan: CRUD & Eloquent ORM',
            ],
            [
                'deskripsi' => 'Buat fitur CRUD lengkap dengan relasi Eloquent dan validasi form pada aplikasi Laravel.',
                'deadline' => now()->addDays(7),
            ]
        );

        $assignment2 = Assignment::updateOrCreate(
            [
                'course_id' => $course2->id,
                'judul' => 'Studi Kasus: BPMN & Use Case Diagram',
            ],
            [
                'deskripsi' => 'Susun diagram proses bisnis to-be dan spesifikasi kebutuhan fungsional sistem informasi.',
                'deadline' => now()->addDays(10),
            ]
        );

        // d. Contoh data Submission
        Submission::updateOrCreate(
            [
                'assignment_id' => $assignment1->id,
                'student_id' => $mahasiswa1->id,
            ],
            [
                'catatan' => 'Repository GitHub dan tangkapan layar pengujian sudah dilampirkan.',
                'file_path' => 'submissions/ahmad_pratama_tp_crud.pdf',
                'nilai' => 90,
            ]
        );

        Submission::updateOrCreate(
            [
                'assignment_id' => $assignment1->id,
                'student_id' => $mahasiswa2->id,
            ],
            [
                'catatan' => 'Sudah selesai dikerjakan dan siap dinilai.',
                'file_path' => 'submissions/siti_rahmawati_tp_crud.pdf',
                'nilai' => null,
            ]
        );
    }
}