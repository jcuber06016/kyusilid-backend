<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;

class QuizController extends Controller
{
    public function getQuiz($id)
    {
        $quiz = Quiz::find($id);
        return response()->json($quiz);
    }


    public function getQuizTitleAll()
    {
        $quiz = Quiz::all();
        return response()->json($quiz);
    }



    public function addQuizQuestion(Request $request)
    {

        $quiz = new Quiz;
        $quiz->title = $request->title;
        $quiz->description = $request->description;
        $quiz->question = $request->questions;
        $quiz->save();
        return $quiz;
    }


    public function updateQuizQuestion(Request $request, $id)
    {
        $quiz = Quiz::find($id);
        $quiz->title = $request->title;
        $quiz->question = $request->questions;
        $quiz->save();
        return $quiz;
    }
    public function deleteQuizQuestion($id)
    {
        $quiz = Quiz::find($id);
        $quiz->delete();
        return response()->json($quiz);
    }


    public function addQuiz(Request $request)
    {
        // $quiz = new Quiztitle;
        // $quiz->title = $request->title;
        // $quiz->description = $request->description;
        // $quiz->save();
        // return response()->json($quiz);


            $temp1 = $request->input('title');
            $temp2 = $request ->input('description');
            $temp3 = $request->input('questions');
            $temp4 = $request->input('category');

            return [
                "title" => $temp1,
                "description" => $temp2,
                "questions" => $temp3,
                "category" => $temp4
            ];

    }


    public function sample(){
        return "sample";
    }

}
