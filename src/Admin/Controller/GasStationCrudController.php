<?php

namespace App\Admin\Controller;

use App\Admin\Filter\GasStationStatusFilter;
use App\Entity\GasStation;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
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
            ->add('company')
            ->add('pop')
            ->add(GasStationStatusFilter::new('status'))
            ->add(TextFilter::new('address'))
            ->add(DateTimeFilter::new('createdAt'))
            ->add(DateTimeFilter::new('updatedAt'))
            ->add(DateTimeFilter::new('closedAt'));
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addPanel('Gas Station Details'),
            IdField::new('gasStationId'),
            TextField::new('hash')->hideOnIndex(),
            TextField::new('pop')->hideOnIndex(),
            TextField::new('name'),
            TextField::new('company'),
            TextField::new('googlePlaceId')->onlyOnIndex(),
            TextField::new('status'),
            ArrayField::new('statuses')->hideOnIndex(),
            CodeEditorField::new('lastGasPricesAdmin')->hideOnIndex()->hideOnForm()->setLabel('lastGasPrices')->setNumOfRows(100),
            Field::new('previousGasPricesAdmin')->hideOnIndex()->hideOnForm()->setLabel('previousGasPrices'),

            FormField::addPanel('Gas Station Address'),
            AssociationField::new('address')->hideOnIndex(),

            FormField::addPanel('Gas Station Google Place'),
            AssociationField::new('googlePlace')->hideOnIndex(),

            FormField::addPanel('Gas Station Services'),
            ArrayField::new('gasServices')->hideOnIndex()->hideOnForm(),

            FormField::addPanel('Gas Station Metadata'),
            DateTimeField::new('createdAt')
                ->setFormat('dd/MM/Y HH:mm:ss')
                ->renderAsNativeWidget()
                ->hideOnForm()
                ->hideOnIndex(),
            DateTimeField::new('updatedAt')
                ->setFormat('dd/MM/Y HH:mm:ss')
                ->renderAsNativeWidget()
                ->hideOnForm()
                ->hideOnForm(),
            DateTimeField::new('closedAt')
                ->setFormat('dd/MM/Y HH:mm:ss')
                ->renderAsNativeWidget()
                ->hideOnForm()
                ->hideOnForm(),

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
            // CodeEditorField::new('positionStackApiResultAdmin')->hideOnIndex()->setDisabled()->setLabel('PositionStackApiResult'),
            // CodeEditorField::new('textsearchApiResultAdmin')->hideOnIndex()->setDisabled()->setLabel('TextsearchApiResult'),
            // CodeEditorField::new('placeDetailsApiResultAdmin')->hideOnIndex()->setDisabled()->setLabel('PlaceDetailsApiResultAdmin'),
        ];
    }
}
