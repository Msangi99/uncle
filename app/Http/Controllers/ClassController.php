<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassController extends Controller
{
    public function index()
    {
        $classes = Classe::orderBy('name')->get();

        return view('classes.index', compact('classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'is_exam_class' => ['sometimes', 'boolean'],
        ]);
        $validated['is_exam_class'] = $request->boolean('is_exam_class');

        Classe::create($validated);

        return redirect()->route('classes.index')->with('success', __('Darasa limeongezwa.'));
    }

    public function update(Request $request, Classe $classe)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'is_exam_class' => ['sometimes', 'boolean'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('classes.index')
                ->withErrors($validator)
                ->withInput()
                ->with('edit_id', $classe->id);
        }

        $data = $validator->validated();
        $data['is_exam_class'] = $request->boolean('is_exam_class');
        $classe->update($data);

        return redirect()->route('classes.index')->with('success', __('Darasa limebadilishwa.'));
    }

    public function destroy(Classe $classe)
    {
        $classe->delete();

        return redirect()->route('classes.index')->with('success', __('Darasa limefutwa.'));
    }
}
