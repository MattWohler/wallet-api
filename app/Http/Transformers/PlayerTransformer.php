<?php declare(strict_types=1);

namespace App\Http\Transformers;

use App\Models\Wallet\Player;

class PlayerTransformer extends AbstractTransformer
{
    protected function attributes(Player $player): array
    {
        $data = [
            'account' => $player->account,
            'title' => $player->title,
            'firstName' => $player->firstName,
            'lastName' => $player->lastName,
            'country' => $player->country,
            'brand' => $player->brand,
            'brandId' => $player->brandId,
            'balance' => [
                'amount' => $player->balance,
                'currency' => $player->currency,
            ],
            'enable' => [
                'casino' => $player->enableCasino,
                'cards' => $player->enableCards,
                'horses' => $player->enableHorses,
                'sports' => $player->enableSports,
            ],
            'test' => $player->isTestAccount
        ];

        if ($player->balance === null) {
            unset($data['balance']);
            $data['currency'] = $player->currency;
        }

        return $data;
    }
}
