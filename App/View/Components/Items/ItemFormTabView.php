        if ($isEdit && !empty($item['image'])) {
            $html .= '<div class="mb-3 p-3 bg-gray-50 inline-block rounded-lg">
                            <img src="' . htmlspecialchars($item['image']) . '" alt="Image actuelle" class="max-w-[200px] rounded">
                            <p class="text-sm text-gray-600 mt-2"><i class="fas fa-info-circle mr-1"></i>Image actuelle</p>
                        </div>';
        } 