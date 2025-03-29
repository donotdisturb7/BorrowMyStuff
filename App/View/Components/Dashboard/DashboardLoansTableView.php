<?php

namespace App\View\Components\Dashboard;

class DashboardLoansTableView {
    /**
     * Render the dashboard loans table
     * 
     * @param array $loans Loans data
     * @return string The HTML for the loans table
     */
    public static function render($loans) {
        $html = '
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Demandes de prêt en attente</h3>
                <p class="mt-1 text-sm text-gray-600">Liste des demandes qui nécessitent votre approbation.</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Objet</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Demandeur</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date de demande</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Période</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">';
                    
        if (empty($loans)) {
            $html .= '<tr><td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Aucune demande de prêt en attente.</td></tr>';
        } else {
            foreach ($loans as $loan) {
                $image = !empty($loan['image_url']) ? '<img src="/' . htmlspecialchars($loan['image_url']) . '" alt="' . htmlspecialchars($loan['item_name']) . '" class="h-10 w-10 rounded object-cover">' : 
                                               '<div class="h-10 w-10 rounded bg-gray-200 flex items-center justify-center"><span class="material-icons-outlined text-gray-400">inventory_2</span></div>';
                
                $html .= '
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            ' . $image . '
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">' . htmlspecialchars($loan['item_name']) . '</div>
                                <div class="text-sm text-gray-500">ID: ' . $loan['item_id'] . '</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">' . htmlspecialchars($loan['requester_name']) . '</div>
                        <div class="text-sm text-gray-500">' . htmlspecialchars($loan['requester_email']) . '</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ' . date('d/m/Y', strtotime($loan['request_date'])) . '
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ' . date('d/m/Y', strtotime($loan['start_date'])) . ' - ' . date('d/m/Y', strtotime($loan['end_date'])) . '
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex space-x-2">
                            <form action="/loans/' . $loan['id'] . '/approve" method="POST" class="inline">
                                <input type="hidden" name="notes" value="">
                                <button type="submit" class="bg-black text-white px-3 py-1.5 rounded text-xs font-medium flex items-center">
                                    <span class="material-icons-outlined text-sm mr-1">check</span>
                                    Approuver
                                </button>
                            </form>
                            <form action="/loans/' . $loan['id'] . '/reject" method="POST" class="inline">
                                <input type="hidden" name="notes" value="">
                                <button type="submit" class="bg-white border border-gray-300 text-gray-700 px-3 py-1.5 rounded text-xs font-medium flex items-center">
                                    <span class="material-icons-outlined text-sm mr-1">close</span>
                                    Refuser
                                </button>
                            </form>
                            <a href="/loans/' . $loan['id'] . '" class="text-primary hover:text-primary-dark px-3 py-1.5">
                                <span class="material-icons-outlined text-sm">info</span>
                            </a>
                        </div>
                    </td>
                </tr>';
            }
        }
        
        $html .= '
                    </tbody>
                </table>
            </div>
        </div>';
        
        return $html;
    }
}
