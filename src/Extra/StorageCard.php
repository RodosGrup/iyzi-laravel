<?php

namespace RodosGrup\IyziLaravel\Extra;

use RodosGrup\IyziLaravel\Models\IyzicoUser;
use RodosGrup\IyziLaravel\Models\StoredCreditCard;
use Illuminate\Support\Str;

class StorageCard
{
    public static function addCreditCard(array $attributes = [], string $email, string $user)
    {

        $control = IyzicoUser::where('email', $email)
            ->orWhere('user_key', $attributes['cardUserKey'])
            ->first() ?? false;

        if (!$control) {
            IyzicoUser::create([
                'user_key' => $attributes['cardUserKey'],
                'user_id' => $user,
                'email' => $email
            ])
                ->cards()
                ->updateOrCreate([
                    'card_token' => $attributes['cardToken'],
                    'user_key' => $attributes['cardUserKey'],
                    'card_bin_number' => $attributes['binNumber'],
                    'card_last_four_digits' => $attributes['lastFourDigits'],
                    'card_alias' => $attributes['cardAlias'],
                    'card_association' => $attributes['cardAssociation'],
                    'card_bank_name' => $attributes['cardBankName'],
                    'card_type' => $attributes['cardType']
                ]);

            // return;
        }

        if (!StoredCreditCard::whereCardBinNumber($attributes['binNumber'])->whereCardLastFourDigits($attributes['lastFourDigits'])->first()) {
            if (isset($attributes['cardAssociation']) && isset($attributes['cardBankName'])) {
                StoredCreditCard::create([
                    'card_token' => $attributes['cardToken'],
                    'user_key' => $attributes['cardUserKey'],
                    'card_bin_number' => $attributes['binNumber'],
                    'card_last_four_digits' => $attributes['lastFourDigits'],
                    'card_alias' => $attributes['cardAlias'],
                    'card_association' => $attributes['cardAssociation'],
                    'card_bank_name' => $attributes['cardBankName'],
                    'card_type' => $attributes['cardType']
                ]);
            }
        }
    }

    public static function userFind(string $email = null)
    {
        return IyzicoUser::where('email', $email)
            ->first()->user_key ?? false;
    }

    public static function cardFind(string $cardNumber)
    {
        $binNumber = Str::substr($cardNumber, 0, 8);
        $lastFourDigits = Str::substr($cardNumber, -4);

        return StoredCreditCard::whereCardBinNumber($binNumber)
            ->whereCardLastFourDigits($lastFourDigits)
            ->first() ?? false;
    }

    public static function cardDelete(string $userKey = null, string $cardToken)
    {
        StoredCreditCard::whereUserKey($userKey)
            ->whereCardToken($cardToken)
            ->delete();
    }
}
