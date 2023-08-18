<?php

namespace App\Admin\Controller;

use App\Admin\Filter\GasStationStatusFilter;
use App\Entity\GasStation;
use App\Lists\GasStationStatusReference;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
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

    public function configureAssets(Assets $assets): Assets
    {
        return $assets
            ->addWebpackEncoreEntry('admin');
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
        $gasStationStatus = [
            GasStationStatusReference::UPDATED_TO_ADDRESS_FORMATED => GasStationStatusReference::UPDATED_TO_ADDRESS_FORMATED,
            GasStationStatusReference::UPDATED_TO_FOUND_IN_TEXTSEARCH => GasStationStatusReference::UPDATED_TO_FOUND_IN_TEXTSEARCH,
            GasStationStatusReference::UPDATED_TO_FOUND_IN_DETAILS => GasStationStatusReference::UPDATED_TO_FOUND_IN_DETAILS,
            GasStationStatusReference::CLOSED => GasStationStatusReference::CLOSED,
            GasStationStatusReference::OPEN => GasStationStatusReference::OPEN,
        ];

        return [
            FormField::addPanel('Gas Station Details'),
            IdField::new('gasStationId')
                ->setDisabled()
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('hash')
                ->hideOnIndex()
                ->setDisabled()
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('name')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('pop')
                ->hideOnIndex()
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),

            FormField::addPanel('Status'),
            TextField::new('status')
                ->setDisabled()
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            ChoiceField::new('statusAdmin')
                ->setLabel('Change status')
                ->hideOnIndex()
                ->autocomplete()
                ->renderAsNativeWidget()
                ->setChoices($gasStationStatus)
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            ArrayField::new('statusesAdmin')
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('Status History')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),

            FormField::addPanel('Prices'),
            CodeEditorField::new('lastGasPricesAdmin')
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('lastGasPrices'),
            CodeEditorField::new('previousGasPricesAdmin')
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('previousGasPrices'),

            FormField::addPanel('GooglePlace'),
            IdField::new('googlePlace.uuid')
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('uuid')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.placeId')
                ->hideOnIndex()
                ->setLabel('placeId')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.googleId')
                ->hideOnIndex()
                ->setLabel('googleId')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.website')
                ->hideOnIndex()
                ->setLabel('website')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.phoneNumber')
                ->hideOnIndex()
                ->setLabel('phoneNumber')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.compoundCode')
                ->hideOnIndex()
                ->setLabel('compoundCode')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.globalCode')
                ->hideOnIndex()
                ->setLabel('globalCode')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.googleRating')
                ->hideOnIndex()
                ->setLabel('googleRating')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.rating')
                ->hideOnIndex()
                ->setLabel('rating')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.userRatingsTotal')
                ->hideOnIndex()
                ->setLabel('userRatingsTotal')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.icon')
                ->hideOnIndex()
                ->setLabel('icon')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.reference')
                ->hideOnIndex()
                ->setLabel('reference')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.wheelchairAccessibleEntrance')
                ->hideOnIndex()
                ->setLabel('wheelchairAccessibleEntrance')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('googlePlace.businessStatus')
                ->hideOnIndex()
                ->setLabel('businessStatus')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            ArrayField::new('googlePlace.openingHours')
                ->hideOnIndex()
                ->setLabel('openingHours')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),

            FormField::addPanel('Address'),
            IdField::new('address.uuid')
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('Uuid')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('address.vicinity')
                ->hideOnIndex()
                ->setLabel('vicinity')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('address.street')
                ->hideOnIndex()
                ->setLabel('street')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('address.number')
                ->hideOnIndex()
                ->setLabel('number')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('address.city')
                ->hideOnIndex()
                ->setLabel('city')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('address.region')
                ->hideOnIndex()
                ->setLabel('region')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('address.postalCode')
                ->hideOnIndex()
                ->setLabel('postalCode')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('address.country')
                ->hideOnIndex()
                ->setLabel('country')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('address.longitude')
                ->hideOnIndex()
                ->setLabel('longitude')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            TextField::new('address.latitude')
                ->hideOnIndex()
                ->setLabel('latitude')
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            DateTimeField::new('address.createdAt')
                ->setFormat('dd/MM/Y HH:mm:ss')
                ->renderAsNativeWidget()
                ->hideOnIndex()
                ->setDisabled()
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),
            DateTimeField::new('address.updatedAt')
                ->setFormat('dd/MM/Y HH:mm:ss')
                ->renderAsNativeWidget()
                ->hideOnIndex()
                ->setDisabled()
                ->setColumns('col-sm-6 col-lg-6 col-xxl-3'),

            FormField::addPanel('Gas Station Brand'),
            AssociationField::new('gasStationBrand'),

            FormField::addPanel('Gas Station Services'),
            CollectionField::new('gasServices')
                ->hideOnIndex(),

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
            TextField::new('image.name', 'Name')
                ->setDisabled()
                ->hideOnIndex(),
            TextField::new('image.originalName', 'originalName')
                ->setDisabled()
                ->hideOnIndex(),
            NumberField::new('image.size', 'Size')
                ->setDisabled()
                ->hideOnIndex(),
            TextField::new('image.mimeType', 'mimeType')
                ->setDisabled()
                ->hideOnIndex(),
            ArrayField::new('image.dimensions', 'Dimensions')
                ->setDisabled()
                ->hideOnIndex(),

            FormField::addPanel('Json fields'),
            CodeEditorField::new('elementAdmin')
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('Element'),
            CodeEditorField::new('googlePlace.textsearchApiResultAdmin')
                ->hideOnIndex()
                ->setDisabled()
                ->setLabel('TextsearchApiResult'),
            // CodeEditorField::new('googlePlace.placeDetailsApiResultAdmin')
            //     ->hideOnIndex()
            //     ->setDisabled()
            //     ->setLabel('PlaceDetailsApiResultAdmin'),

            // CodeEditorField::new('address.positionStackApiResultAdmin')
            //     ->hideOnIndex()
            //     ->setDisabled()
            //     ->setLabel('positionStackApiResult'),
        ];
    }
}
