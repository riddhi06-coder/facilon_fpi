<?php

namespace App\Http\Controllers;

class UboFpiController extends Controller
{
    /**
     * Default ownership hierarchy used to seed the UBO determination tool.
     * The evaluation itself runs client-side (see the view).
     */
    private function defaultEntities(): array
    {
        return [
            [
                'id' => 'applicant',
                'name' => 'GLOBAL ALPHAS FPI FUND',
                'type' => 'Partnership',
                'owners' => [
                    ['id' => 'own1', 'name' => 'Alpha Holdings LLC', 'type' => 'Entity', 'pct' => 60, 'targetId' => 'alpha_holdings'],
                    ['id' => 'own2', 'name' => 'Johnathan Davis', 'type' => 'Individual', 'pct' => 40],
                ],
            ],
            [
                'id' => 'alpha_holdings',
                'name' => 'Alpha Holdings LLC',
                'type' => 'Company',
                'owners' => [
                    ['id' => 'own3', 'name' => 'Sarah Jenkins', 'type' => 'Individual', 'pct' => 80],
                    ['id' => 'own4', 'name' => 'Mike Smith', 'type' => 'Individual', 'pct' => 20],
                ],
            ],
        ];
    }

    public function index()
    {
        $entities = $this->defaultEntities();

        return view('ubo-fpi.index', compact('entities'));
    }
}
