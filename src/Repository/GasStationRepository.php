<?php

namespace App\Repository;

use App\Entity\GasStation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GasStation>
 *
 * @method GasStation|null find($id, $lockMode = null, $lockVersion = null)
 * @method GasStation|null findOneBy(array $criteria, array $orderBy = null)
 * @method GasStation[]    findAll()
 * @method GasStation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GasStationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GasStation::class);
    }

    /**
     * @return []
     */
    public function findGasStationsById()
    {
        $query = $this->createQueryBuilder('s')
            ->indexBy('s', 's.gasStationId')
            ->select('s.gasStationId, s.hash')
            ->getQuery();

        return $query->getArrayResult();
    }

    /** @return GasStation[] */
    public function getGasStationsMap(string $longitude, string $latitude, string $radius, string $gasTypeUuid, array $filterCity)
    {
        $gasTypeFilter = $this->createGasTypeFilter($gasTypeUuid);
        $cityFilter = $this->createGasStationsCitiesFilter($filterCity);

        $query = "  SELECT 
                    s.id, 
                    (SQRT(POW(69.1 * (a.latitude - $latitude), 2) + POW(69.1 * ($longitude - a.longitude) * COS(a.latitude / 57.3), 2))*1000) as distance,
                    
                    JSON_KEYS(s.last_gas_prices) as gas_types,
                    
                    (SELECT GROUP_CONCAT(gs.name SEPARATOR ', ')
                    FROM gas_service_gas_station gss
                    INNER JOIN gas_service gs ON gss.gas_service_id = gs.id
                    AND gss.gas_station_id = s.id) as gas_services
  
                    FROM gas_station s 
                    INNER JOIN address a ON s.address_id = a.id
                    WHERE a.longitude IS NOT NULL AND a.latitude IS NOT NULL $gasTypeFilter $cityFilter
                    HAVING `distance` < $radius
                    ORDER BY `distance` ASC LIMIT 50;
        ";

        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $query = $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.id IN (:ids)')
            ->setParameter('ids', $statement->executeQuery()->fetchFirstColumn())
            ->getQuery();
        return $query->getResult();
    }

    private function createGasTypeFilter(string $gasTypeUuid)
    {
        $query = " AND (JSON_KEYS(s.last_gas_prices) LIKE '%$gasTypeUuid%')";
        return $query;
    }

    private function createGasServicesFilter($filters)
    {
        $query = '';
        if (array_key_exists('gas_service', $filters ?? []) && '' !== $filters['gas_service']) {
            $gasServices = explode(',', $filters['gas_service']);
            $query = ' AND (';
            foreach ($gasServices as $gasService) {
                $query .= "`gas_services` LIKE '%" . trim($gasService) . "%' OR ";
            }
            $query = mb_substr($query, 0, -4);
            $query .= ')';
        }

        return $query;
    }

    private function createGasStationsCitiesFilter(array $filterCity)
    {
        $query = '';
        if (count($filterCity) >= 1) {
            $cities = implode(",", $filterCity);
            $query = " AND a.postal_code IN ($cities)";
        }

        return $query;
    }

    private function createGasStationsDepartmentsFilter($filters)
    {
        $query = '';
        if (array_key_exists('department', $filters ?? []) && '' !== $filters['department']) {
            $departments = $filters['department'];
            $query = " AND SUBSTRING(a.postal_code, 1, 2) IN ($departments)";
        }

        return $query;
    }
}
