'use client';

import * as sprintf from 'sprintf-js';
import {GoogleMap, InfoWindow, Marker, useJsApiLoader} from '@react-google-maps/api';
import {SetStateAction, useEffect, useMemo, useRef, useState} from "react";
import Loader from "@/components/Loader";
import {Button, Dropdown, DropdownItem, DropdownMenu, DropdownTrigger} from "@nextui-org/react";
import {FaLocationDot} from "react-icons/fa6";
import {Key} from "swr";

const initialMapCenter = {
    lat: 48.853,
    lng: 2.35,
};

export default function Home() {

    const {isLoaded} = useJsApiLoader({
        id: 'google-map-script',
        googleMapsApiKey: process.env.NEXT_PUBLIC_GOOGLE_API_KEY as string
    })

    let mapRef = useRef<google.maps.Map | null>(null);
    const [markersData, setMarkersData] = useState([]);
    const [gasTypesData, setGasTypesData] = useState([]);
    const [mapCenter, setMapCenter] = useState(initialMapCenter);
    const [selectedMarker, setSelectedMarker] = useState(null);
    const [selectedGasType, setSelectedGasType] = useState(new Set(["E10"]));
    const [gasTypeUuid, setGasTypeUuid] = useState(process.env.NEXT_PUBLIC_GAS_TYPE_UUID as string);

    const selectedValue = useMemo(
        () => Array.from(selectedGasType).join(", ").replaceAll("_", " "),
        [selectedGasType]
    );

    const onGasTypesChange = (key: Key) => {
        let uuid = gasTypeUuid;
        gasTypesData.map((type, index) => {
            if (type['name'] === key) {
                uuid = type['uuid'];
                setGasTypeUuid(type['uuid']);
            }
        });

        const url: string = process.env.NEXT_PUBLIC_GAS_STATIONS_MAP as string;
        const formattedString = sprintf.sprintf(url + "?latitude=%s&longitude=%s&zoom=%s&gas_type_uuid=%s", mapCenter.lat, mapCenter.lng, mapRef.current?.getZoom(), uuid);
        fetchGasStationsUrl(formattedString);
    };

    const handleMarkerClick = (marker: SetStateAction<null>) => {
        setSelectedMarker(marker);
    };

    const handleMapLoad = (map: google.maps.Map | null) => {
        console.log("handleMapLoad");

        const url: string = process.env.NEXT_PUBLIC_GAS_TYPES as string;
        fetchGasTypesUrl(url);

        navigator.geolocation.getCurrentPosition(
            function(position) {
                userFound(position, map);
            },
            function (positionError) {
                userNotFound(map);
            }
        );

        mapRef.current = map;
    };

    const userFound = (position: GeolocationPosition, map: google.maps.Map | null) => {
        const url: string = process.env.NEXT_PUBLIC_GAS_STATIONS_MAP as string;
        const center = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
        const formattedString = sprintf.sprintf(url + "?latitude=%s&longitude=%s&zoom=%s&gas_type_uuid=%s", center.lat, center.lng, map?.getZoom(), gasTypeUuid);
        fetchGasStationsUrl(formattedString);
        map?.setCenter(center);
    }

    const userNotFound = (map: google.maps.Map | null) => {
        const url: string = process.env.NEXT_PUBLIC_GAS_STATIONS_MAP as string;
        const center = new google.maps.LatLng(initialMapCenter.lat, initialMapCenter.lng);
        const formattedString = sprintf.sprintf(url + "?latitude=%s&longitude=%s&zoom=%s&gas_type_uuid=%s", initialMapCenter.lat, initialMapCenter.lng, map?.getZoom(), gasTypeUuid);
        fetchGasStationsUrl(formattedString);
        map?.setCenter(center);
    }

    const fetchGasStationsUrl = (url: string) => {
        fetch(url)
            .then((response) => response.json())
            .then((data) => {
                setMarkersData(data['hydra:member']);
            });
    }

    const fetchGasTypesUrl = (url: string) => {
        fetch(url)
            .then((response) => response.json())
            .then((data) => {
                setGasTypesData(data['hydra:member']);
            });
    }

    const handleMapDragEnd = () => {
        console.log('handleMapDragEnd')
        console.log(gasTypeUuid)

        const map = mapRef.current;
        if (!map) return;

        const center = map.getCenter();
        if (!center) return;

        const newCenter: google.maps.LatLngLiteral = center.toJSON();
        setMapCenter(newCenter);

        const url: string = process.env.NEXT_PUBLIC_GAS_STATIONS_MAP as string;
        const formattedString = sprintf.sprintf(url + "?latitude=%s&longitude=%s&zoom=%s&gas_type_uuid=%s", newCenter.lat, newCenter.lng, map.getZoom(), gasTypeUuid);
        fetchGasStationsUrl(formattedString);
    };

    const popUp = (marker: never) => {
        return (
            <div className={'stations_map'}>
                <img src={process.env.NEXT_PUBLIC_GAS_BACK_URL + marker['imagePath']} alt="Marker Image" />
                <h3>{marker['name']}</h3>
                <p className={'address_street'}><FaLocationDot /> {marker['address']['number']} {marker['address']['street']}</p>
                <p className={'address_city'}>{marker['address']['postalCode']}, {marker['address']['city']}</p>
                <a className={'link'} href={'gas_station/' + marker['uuid']} target="_blank">Voir plus</a>
            </div>
        );
    }

    useEffect(() => {
    }, []);

    return (
            isLoaded ? (
                <>

            <Dropdown>
                <DropdownTrigger>
                    <Button
                        variant="bordered"
                        className="capitalize"
                    >
                        {selectedValue}
                    </Button>
                </DropdownTrigger>
                <DropdownMenu
                    onAction={(key) => onGasTypesChange(key as string)}
                    aria-label="Single selection example"
                    variant="flat"
                    disallowEmptySelection
                    selectionMode="single"
                    selectedKeys={selectedGasType}
                    onSelectionChange={setSelectedGasType}
                >
                    {
                        gasTypesData.map((type, index) => (
                            <DropdownItem key={type['name']}>{type['name']}</DropdownItem>
                        ))
                    }
                </DropdownMenu>
            </Dropdown>

                <GoogleMap
                    mapContainerClassName="map-container"
                    options={{
                        disableDefaultUI: false,
                        clickableIcons: false,
                        scrollwheel: true,
                        fullscreenControl: false,
                        keyboardShortcuts: false,
                        rotateControl: false,
                        streetViewControl: false,
                        mapTypeControl: true,
                        mapTypeControlOptions: {
                            mapTypeIds: ['roadmap'],
                        },
                    }}
                    mapContainerStyle={{
                        width: '100%',
                        height: 'calc(100% - 4rem)',
                    }}
                    zoom={13}
                    center={mapCenter}
                    onLoad={handleMapLoad}
                    onDragEnd={handleMapDragEnd}
                    onClick={() => setSelectedMarker(null)}
                    onZoomChanged={handleMapDragEnd}
                >
                    {
                        Array.isArray(markersData) && markersData.map((marker, index) => (
                            <Marker
                                key={marker["uuid"]}
                                onClick={() => handleMarkerClick(marker)}
                                position={{ lat: parseFloat(marker["address"]["latitude"]), lng: parseFloat(marker["address"]["longitude"]) }}
                            >
                            </Marker>
                        ))
                    }


                    {selectedMarker && (
                        <InfoWindow
                            position={{ lat: parseFloat(selectedMarker["address"]["latitude"]), lng: parseFloat(selectedMarker["address"]["longitude"]) }}
                            onCloseClick={() => setSelectedMarker(null)}
                        >
                            {popUp(selectedMarker)}
                        </InfoWindow>
                    )}

                </GoogleMap></>
            ) : (
                <Loader></Loader>
            )
  );
}
