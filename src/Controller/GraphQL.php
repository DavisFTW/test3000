<?php

namespace App\Controller;

use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use App\Database\Database;
use App\Model\Product;

class GraphQL {
    public function handle() {
        $priceType = new ObjectType([
            'name' => 'Price',
            'fields' => [
                'amount' => ['type' => Type::float()],
                'currency' => ['type' => new ObjectType([
                    'name' => 'Currency',
                    'fields' => [
                        'label' => ['type' => Type::string()],
                        'symbol' => ['type' => Type::string()]
                    ]
                ])]
            ]
        ]);

        $attributeType = new ObjectType([
            'name' => 'Attribute',
            'fields' => [
                'id' => ['type' => Type::string()],
                'displayValue' => ['type' => Type::string()],
                'value' => ['type' => Type::string()]
            ]
        ]);

        $attributeSetType = new ObjectType([
            'name' => 'AttributeSet',
            'fields' => [
                'id' => ['type' => Type::string()],
                'name' => ['type' => Type::string()],
                'type' => ['type' => Type::string()],
                'items' => ['type' => Type::listOf($attributeType)]
            ]
        ]);

        $productType = new ObjectType([
            'name' => 'Product',
            'fields' => [
                'id' => ['type' => Type::string()],
                'name' => ['type' => Type::string()],
                'inStock' => ['type' => Type::boolean()],
                'gallery' => ['type' => Type::listOf(Type::string())],
                'description' => ['type' => Type::string()],
                'category' => ['type' => Type::string()],
                'attributes' => ['type' => Type::listOf($attributeSetType)],
                'prices' => ['type' => Type::listOf($priceType)],
                'brand' => ['type' => Type::string()]
            ]
        ]);

        $queryType = new ObjectType([
            'name' => 'Query',
            'fields' => [
                'products' => [
                    'type' => Type::listOf($productType),
                    'resolve' => [$this, 'resolveProducts']
                ],
                'echo' => [
                    'type' => Type::string(),
                    'args' => [
                        'message' => ['type' => Type::string()],
                    ],
                    'resolve' => [$this, 'resolveEcho']
                ],
            ],
        ]);

        $mutationType = new ObjectType([
            'name' => 'Mutation',
            'fields' => [
                'sum' => [
                    'type' => Type::int(),
                    'args' => [
                        'x' => ['type' => Type::int()],
                        'y' => ['type' => Type::int()],
                    ],
                    'resolve' => [$this, 'resolveSum']
                ],
            ],
        ]);

        $schema = new Schema(
            (new SchemaConfig())
            ->setQuery($queryType)
            ->setMutation($mutationType)
        );

        $rawInput = file_get_contents('php://input');
        if ($rawInput === false) {
            $this->handleError('Failed to get php://input');
            return;
        }

        $input = json_decode($rawInput, true);
        if ($input === null) {
            $this->handleError('Failed to decode JSON input.');
            return;
        }
        $query = $input['query'] ?? '';
        if (empty($query)) {
            $this->handleError('No query provided.');
            return;
        }
        $variableValues = $input['variables'] ?? null;

        $rootValue = ['prefix' => 'You said: '];
        $result = GraphQLBase::executeQuery($schema, $query, $rootValue, null, $variableValues);
        $output = $result->toArray();

        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($output);
    }

    public function resolveProducts() {
        $database = new Database();
        $db = $database->getConnection();
        if (!$db) {
            $this->handleError('Failed to connect to the database.');
            return [];
        }
        $product = new Product($db);
        $products = $product->readProduct();
        if (!$products) {
            $this->handleError('No products found.');
            return [];
        }
        return $products;
    }

    public function resolveEcho($rootValue, array $args): string {
        return $rootValue['prefix'] . $args['message'];
    }

    public function resolveSum($calc, array $args): int {
        return $args['x'] + $args['y'];
    }

    private function handleError($message) {
        error_log('Error: ' . $message);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'error' => [
                'message' => $message,
            ],
        ]);
    }
}
