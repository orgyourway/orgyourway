<?php

namespace App\Controller\Utility;

use App\Form\UploadUsersCsvForm;
use App\Service\UploadUsersCsvLoader;
use App\Service\UploadUsersCsvPersister;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/utility')]
class UploadUsersCsvController extends AbstractController
{
    private UploadUsersCsvLoader $uploadUsersCsvLoader;
    private UploadUsersCsvPersister $uploadUsersCsvPersister;

    public function __construct(
        UploadUsersCsvLoader $uploadUsersCsvLoader,
        UploadUsersCsvPersister $uploadUsersCsvPersister
    ) {
        $this->uploadUsersCsvLoader = $uploadUsersCsvLoader;
        $this->uploadUsersCsvPersister = $uploadUsersCsvPersister;
    }

    #[Route('/csv/upload/users', name: 'app_utility_upload_csv_users')]
    public function index(): Response
    {
        $csv_data = [];
        $form = $this->createForm(UploadUsersCsvForm::class);

        return $this->render('utility_upload_csv_users/index.html.twig', [
            'controller_name' => 'UploadUsersCsvController',
        ]);
    }
}
