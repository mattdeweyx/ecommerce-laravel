<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        // Generate a random price rounded to the nearest 5 or 10
        $price = rand(60, 240); // Adjust the range as needed

        return [
            'name' => $this->getRandomPerfumeName(),
            'brand' => $this->getRandomPerfumeBrand(),
            'price' => $price,
            'description' => $this->getRandomDescription(),
            'concentration' => $this->getRandomPerfumeConcentration(),
            'category' => $this->faker->randomElement(['Men', 'Women']),
            'imageURL' => $this->getRandomImageURL(),
            'ratingCount' => rand(4000, 15000),
            'ratingScore' => $this->faker->randomFloat(1, 3, 5),
        ];
    }

    private function getRandomPerfumeName()
    {
        $names = [
            'Seduction',
            'Elegance',
            'Mystique',
            'Enigma',
            'Charm',
            'Desire',
            'Passion',
            'Adoration',
            'Obsession',
            'Fascination',
            'Intrigue',
            'Allure',
            'Whisper',
            'Mystery',
            'Secret',
            'Fantasy',
            'Dream',
            'Illusion',
            'Legend',
            'Legacy',
            'Prestige',
            'Splendor',
            'Euphoria',
            'Harmony',
            'Tranquility',
            'Serenity',
            'Rapture',
            'Bliss',
            'Ecstasy',
            'Radiance',
            'Glow',
            'Sparkle',
            'Luminance',
            'Aura',
            'Ethereal',
            'Celestial',
            'Heavenly',
            'Divine',
            'Paradise',
        ];
        return $names[array_rand($names)];
    }

    private function getRandomPerfumeBrand()
    {
        return $this->faker->randomElement(['Chanel', 'Dior', 'Gucci', 'Prada', 'Jo Malone']);
    }

    private function getRandomDescription()
    {
        $descriptions = [
            'A mesmerizing blend of floral and woody notes.',
            'Captivating scent that lingers all day.',
            'Exudes sophistication and confidence.',
            'Perfect for any occasion, day or night.',
            'Envelops you in a cloud of elegance and charm.',
            'Transport your senses to a world of luxury and indulgence.',
            'An exquisite fragrance that leaves a lasting impression.',
            'Irresistibly alluring and intoxicating.',
            'Experience the allure of this enchanting scent.',
            'Unveil your inner beauty with this captivating perfume.',
            'Evoke a sense of mystery and intrigue with every spray.',
            'Indulge in the opulence of this timeless fragrance.',
            'Embrace the essence of luxury with this divine scent.',
            'Step into a world of elegance and grace with this perfume.',
            'Ignite your senses with this passionate and sensual fragrance.',
            'Experience the magic of this enchanting aroma.',
            'Awaken your senses and ignite your soul with this divine scent.',
            'Discover the secret to timeless elegance with this fragrance.',
            'Embrace your inner goddess with this luxurious perfume.',
            'Unleash your inner beauty with this captivating scent.',
            'Experience the allure of this enchanting fragrance.',
            'Evoke a sense of mystery and intrigue with this captivating perfume.',
            'Indulge in the opulence of this timeless fragrance.',
            'Experience the magic of this enchanting aroma.',
            'Awaken your senses and ignite your soul with this divine scent.',
            'Embrace the essence of luxury with this divine perfume.',
            'Step into a world of elegance and grace with this perfume.',
            'Ignite your senses with this passionate and sensual fragrance.',
            'Awaken your senses with this invigorating perfume.',
            'Discover the secret to timeless elegance with this fragrance.',
            'Embrace your inner goddess with this luxurious perfume.',
            'Unleash your inner beauty with this captivating scent.',
            'Experience the allure of this enchanting fragrance.',
            'Evoke a sense of mystery and intrigue with this captivating perfume.',
            'Indulge in the opulence of this timeless fragrance.',
            'Experience the magic of this enchanting aroma.',
            'Awaken your senses and ignite your soul with this divine scent.',
        ];
        return $descriptions[array_rand($descriptions)];
    }

    private function getRandomPerfumeConcentration()
    {
        return $this->faker->randomElement(['Eau De Parfum', 'Parfum', 'Eau de Toilette', 'Extrait D.P']);
    }

    private function getRandomImageURL()
    
    {
        $imageURLs = [
            'products/diorsauv.png',
            'products/LD5BEfnrU4r0Bbe1ZaoR3OoGkD7GF74cViLVUEte.png',
            'products/pngwing-com.png',
            'products/pngwing-com-1.png',
            // Add more image URLs as needed
        ];
        return $imageURLs[array_rand($imageURLs)];
    }
}
