<?php 

namespace App\Service;

use App\Repository\CategoryRepository;

class NavigationService
{
    public function __construct(private CategoryRepository $categoryRepository)
    {
        

    }

    public function getCategories()
    {
        return $this->categoryRepository->findAll();
    }
}