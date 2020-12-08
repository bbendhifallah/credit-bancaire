<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Reimbursement;

/**
 * Controller used to manage data.
 *
 * @Route("/")
 *
 * @author Bader Ben Dhifallah <bendhifallah.bader@gmail.com>
 */
class CreditController extends AbstractController
{
    /**
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Compute credit
     * 
     * @Route("/", methods="GET", name="credit_index")
     */
    public function index(Request $request): Response
    {
        $defaultData = [];
        $reimbursements = [];

        $form = $this->createFormBuilder($defaultData)
            ->setMethod('GET')
            ->add('amount', NumberType::class, ['label' => 'Montant du crédit'])
            ->add('rate', NumberType::class, ['label' => 'Taux d\'intérêt'])
            ->add('monthlyPayment', NumberType::class, ['label' => 'Mensualité'])
            ->add('send', SubmitType::class, ['label' => 'Calculer'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $params = $form->getData();
            $reimbursements = $this->getReimbursementData($params);
        }


        return $this->render('credit/index.html.twig', [
            'form' => $form->createView(),
            'reimbursements' => $reimbursements,
        ]);
    }
    
    private function getReimbursementData(array $params)
    {
        $monthlyRate = round(($params['rate'] / 12) / 100, 4);
        
        $totalReimbursed = 0;
        $reimbursements = [];
        $due = $params['amount'];
        $month = 1;

        while ($totalReimbursed < $params['amount']) {
            $monthlyInterest = $monthlyRate * $due;
            $monthlyReimbursed = $params['monthlyPayment'] - $monthlyInterest;
            $totalReimbursed = $totalReimbursed + $monthlyReimbursed;
            $due = $params['amount'] - $totalReimbursed;

            if ($due < 0) {
                $due = (float) $params['amount'] - $totalReimbursed;
                $totalReimbursed = (float) $params['amount'];
            }

            $reimbursements[] = new Reimbursement([
                'date' => $month,
                'due' => $due,
                'interest' => $monthlyInterest,
                'reimbursed' => $totalReimbursed,
            ]);

            ++$month;
        }

        return $reimbursements;
    }
}