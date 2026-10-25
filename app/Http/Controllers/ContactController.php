<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{
    // Daftar kontak: search, filter kategori, urutan A-Z / Z-A, dikelompokkan per huruf
    public function index(Request $request)
    {
        $user = $request->user();
        $urut = $request->query('urut') === 'desc' ? 'desc' : 'asc'; // hanya 2 nilai, aman dipakai di orderByRaw

        $query = $user->contacts()->with('category');

        if ($request->filled('q')) {
            $q = $request->query('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('nama', 'like', "%{$q}%")
                    ->orWhere('no_telepon', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('category_id', $request->query('kategori'));
        }

        $contacts = $query->orderByRaw('LOWER(nama) ' . $urut)->get();

        // Deretan huruf untuk strip A-Z di pinggir daftar
        $deret = array_merge(['#'], range('A', 'Z'));
        if ($urut === 'desc') {
            $deret = array_reverse($deret);
        }

        return view('contacts.index', [
            'groups' => $contacts->groupBy('huruf'),
            'total' => $contacts->count(),
            'categories' => $user->categories()->orderBy('nama')->get(),
            'deret' => $deret,
            'urut' => $urut,
        ]);
    }

    public function create(Request $request)
    {
        return view('contacts.create', [
            'categories' => $request->user()->categories()->orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(
            $this->rules($request),
            $this->messages(),
            $this->attributes()
        );

        $contact = $request->user()->contacts()->create($data);

        ActivityLog::catat($request->user()->id, 'kontak_ditambah', "Kontak {$contact->nama} ditambahkan.");

        return redirect()->route('contacts.index')
            ->with('success', 'Kontak berhasil ditambahkan.');
    }

    public function edit(Request $request, Contact $contact)
    {
        $this->pastikanMilikSendiri($request, $contact);

        return view('contacts.edit', [
            'contact' => $contact,
            'categories' => $request->user()->categories()->orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, Contact $contact)
    {
        $this->pastikanMilikSendiri($request, $contact);

        $data = $request->validate(
            $this->rules($request, $contact->id),
            $this->messages(),
            $this->attributes()
        );

        $contact->update($data);

        ActivityLog::catat($request->user()->id, 'kontak_diubah', "Kontak {$contact->nama} diubah.");

        return redirect()->route('contacts.index')
            ->with('success', 'Kontak berhasil diubah.');
    }

    public function destroy(Request $request, Contact $contact)
    {
        $this->pastikanMilikSendiri($request, $contact);

        $nama = $contact->nama;
        $contact->delete();

        ActivityLog::catat($request->user()->id, 'kontak_dihapus', "Kontak {$nama} dihapus.");

        return redirect()->route('contacts.index')
            ->with('success', 'Kontak berhasil dihapus.');
    }

    // Satu akun tidak boleh membuka/mengubah kontak akun lain
    private function pastikanMilikSendiri(Request $request, Contact $contact): void
    {
        abort_unless((int) $contact->user_id === (int) $request->user()->id, 403);
    }

    private function rules(Request $request, ?int $ignoreId = null): array
    {
        $userId = $request->user()->id;

        return [
            'nama' => ['required', 'string', 'max:100'],
            'no_telepon' => ['required', 'regex:/^[0-9+\-\s]{8,20}$/'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('contacts', 'email')->where('user_id', $userId)->ignore($ignoreId),
            ],
            'alamat' => ['nullable', 'string', 'max:500'],
            'tanggal_lahir' => ['nullable', 'date', 'before_or_equal:today'],
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where('user_id', $userId),
            ],
        ];
    }

    private function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'max' => ':attribute maksimal :max karakter.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah dipakai kontak lain.',
            'no_telepon.regex' => 'Nomor telepon hanya boleh angka, +, - dan spasi (8-20 karakter).',
            'tanggal_lahir.date' => 'Format tanggal lahir tidak valid.',
            'tanggal_lahir.before_or_equal' => 'Tanggal lahir tidak boleh di masa depan.',
            'category_id.exists' => 'Kategori tidak valid.',
        ];
    }

    private function attributes(): array
    {
        return [
            'nama' => 'Nama',
            'no_telepon' => 'Nomor telepon',
            'email' => 'Email',
            'alamat' => 'Alamat',
            'tanggal_lahir' => 'Tanggal lahir',
            'category_id' => 'Kategori',
        ];
    }
}
