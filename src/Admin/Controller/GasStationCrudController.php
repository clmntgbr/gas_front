<?php

namespace App\Admin\Controller;

use App\Admin\Filter\GasStationStatusFilter;
use App\Entity\GasStation;
use App\Lists\GasStationStatusReference;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Vich\UploaderBundle\Form\Type\VichImageType;

class GasStationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return GasStation::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['updatedAt' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('gasStationId')
            ->add('pop')
            ->add(GasStationStatusFilter::new('status'))
            ->add(TextFilter::new('address'))
            ->add(DateTimeFilter::new('createdAt'))
            ->add(DateTimeFilter::new('updatedAt'))
            ->add(DateTimeFilter::new('closedAt'));
    }

    public function configureFields(string $pageName): iterable
    {
        $gasStationStatus = GasStationStatusReference::getConstantsList();

        return [
            FormField::addPanel('Gas Station Details'),
            IdField::new('gasStationId')->setDisabled(),
            TextField::new('hash')->hideOnIndex()->setDisabled(),
            TextField::new('pop')->hideOnIndex(),
            TextField::new('name'),
            AssociationField::new('googlePlace')->onlyOnIndex(),
            ChoiceField::new('status')
                ->autocomplete()
                ->renderAsNativeWidget()
                ->setChoices($gasStationStatus),
            ArrayField::new('statuses')
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('Status History'),
            CodeEditorField::new('lastGasPricesAdmin')->hideOnIndex()->hideOnForm()->setLabel('lastGasPrices')->setNumOfRows(100),
            CodeEditorField::new('previousGasPricesAdmin')->hideOnIndex()->hideOnForm()->setLabel('previousGasPrices'),

            FormField::addPanel('Gas Station Address'),
            AssociationField::new('address')->hideOnIndex(),

            FormField::addPanel('Gas Station Brand'),
            AssociationField::new('gasStationBrand'),

            FormField::addPanel('Gas Station Google Place'),
            AssociationField::new('googlePlace')->hideOnIndex(),

            FormField::addPanel('Gas Station Services'),
            CollectionField::new('gasServices')->hideOnIndex(),

            FormField::addPanel('Gas Station Metadata'),
            DateTimeField::new('createdAt')
                ->setFormat('dd/MM/Y HH:mm:ss')
                ->renderAsNativeWidget()
                ->setDisabled()
                ->hideOnIndex(),
            DateTimeField::new('updatedAt')
                ->setFormat('dd/MM/Y HH:mm:ss')
                ->renderAsNativeWidget()
                ->setDisabled(),
            DateTimeField::new('closedAt')
                ->setFormat('dd/MM/Y HH:mm:ss')
                ->renderAsNativeWidget()
                ->setDisabled(),

            FormField::addPanel('Image'),
            TextField::new('imageFile', 'Upload')
                ->setFormType(VichImageType::class)
                ->onlyOnForms(),
            ImageField::new('image.name', 'Image')
                ->setRequired(true)
                ->setBasePath('/images/gas_stations/')
                ->hideOnForm(),
            TextField::new('image.name', 'Name')->setDisabled()->hideOnIndex(),
            TextField::new('image.originalName', 'originalName')->setDisabled()->hideOnIndex(),
            NumberField::new('image.size', 'Size')->setDisabled()->hideOnIndex(),
            TextField::new('image.mimeType', 'mimeType')->setDisabled()->hideOnIndex(),
            ArrayField::new('image.dimensions', 'Dimensions')->setDisabled()->hideOnIndex(),

            FormField::addPanel('Json fields'),
            CodeEditorField::new('elementAdmin')->hideOnIndex()->setDisabled()->setLabel('Element'),
            // CodeEditorField::new('textsearchApiResultAdmin')->hideOnIndex()->setDisabled()->setLabel('TextsearchApiResult'),
            // CodeEditorField::new('placeDetailsApiResultAdmin')->hideOnIndex()->setDisabled()->setLabel('PlaceDetailsApiResultAdmin'),
        ];
    }
}
