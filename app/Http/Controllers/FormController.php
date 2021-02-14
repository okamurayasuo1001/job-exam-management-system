<?php

namespace App\Http\Controllers;

use App\Forms\JobForm;
use App\Models\Form;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;

class FormController extends Controller
{

    /**
     * Admin middleware
     */
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $forms = Form::all();
        return view('admin.forms.index', compact('forms'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(\App\Forms\JobForm::class, [
            'method' => 'POST',
            'url' => route('forms.store')
        ]);

        return view('admin.forms.create', compact('form'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if (isset($request['exam_circular_file'])) {

            $request->validate([
                'exam_circular_file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            ]);

            // $imageName = time() . '.' . $request->exam_circular_file->extension();
            // $imageName = $request->exam_circular_file->storeAs('images', $imageName, 'public');

            $request['exam_circular'] = $request->file('exam_circular_file')->store('jobs', 'public');
        }
        Form::create($request->except(['exam_circular_file']));
        return redirect()->route('forms.index')->with('success', 'Exam created!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Form  $form
     * @return \Illuminate\Http\Response
     */
    public function show(Form $form)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Form  $form
     * @return \Illuminate\Http\Response
     */

    public function edit($id, FormBuilder $formBuilder)
    {
        $jobForm = \App\Models\Form::findOrFail($id);
        // $form = $formBuilder->create(JobForm::class, [
        //     'method' => 'PUT',
        //     'url' => route("forms.update", $jobForm),
        //     'model' => $jobForm,
        // ]);
        $form = $formBuilder->createByArray([
            [
                "name" => "exam_name",
                "type" => "text",
            ],
            [
                "name" => "exam_description",
                "type" => "textarea",
            ],
            [
                "name" => "exam_circular_file",
                "type" => "file",
            ],
            [
                "name" => "exam_date",
                "type" => "date",
                "rules" => ["required"],
                "value" => $jobForm["exam_date"],
            ],
            [
                "name" => "exam_activity_status",
                "type" => "select",
                "choices" => ["taken" => "Taken", "not_taken" => "Not Taken"],
                "selected" => $jobForm["exam_activity_status"]
            ],
            [
                "type" => "submit",
                "name" => "Update"
            ]
        ], [
            'method' => 'PUT',
            'url' => route("forms.update", $jobForm),
            'model' => $jobForm,
        ]);

        return view('admin.forms.edit', compact('form'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Form  $form
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $jobForm = \App\Models\Form::findOrFail($id);
        $jobForm->update($request->all());
        return redirect()->route("forms.index")->with("success", "Updated successfully!");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Form  $form
     * @return \Illuminate\Http\Response
     */
    public function destroy(Form $form)
    {
        //
    }
}
