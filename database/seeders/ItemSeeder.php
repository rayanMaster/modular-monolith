<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\ItemCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate the cities table
        DB::table('items')->truncate();
        $categories = ItemCategory::all()->keyBy('name');
        $items = [
            // Construction Materials
            ['name' => 'Cement', 'description' => 'A binding material used in construction.', 'category' => 'Construction Materials'],
            ['name' => 'Sand', 'description' => 'Fine granular material used in construction.', 'category' => 'Construction Materials'],
            ['name' => 'Gravel', 'description' => 'Small stones used for construction.', 'category' => 'Construction Materials'],
            ['name' => 'Bricks', 'description' => 'Rectangular blocks used in building walls.', 'category' => 'Construction Materials'],
            ['name' => 'Steel Rebars', 'description' => 'Reinforcement bars for concrete structures.', 'category' => 'Construction Materials'],
            ['name' => 'Concrete Blocks', 'description' => 'Precast concrete blocks used in construction.', 'category' => 'Construction Materials'],
            ['name' => 'Timber/Lumber', 'description' => 'Wood used for construction purposes.', 'category' => 'Construction Materials'],
            ['name' => 'Plywood', 'description' => 'Thin layers of wood glued together for construction.', 'category' => 'Construction Materials'],
            ['name' => 'Insulation Materials', 'description' => 'Materials used to insulate buildings.', 'category' => 'Construction Materials'],
            ['name' => 'Drywall Sheets', 'description' => 'Panels used to build interior walls.', 'category' => 'Construction Materials'],
            ['name' => 'Roofing Sheets', 'description' => 'Materials used to cover roofs.', 'category' => 'Construction Materials'],
            ['name' => 'Tiles-120x60 (Floor/Wall)', 'description' => 'Ceramic pieces used for flooring or walls.', 'category' => 'Construction Materials'],
            ['name' => 'Tiles-40x40 (Floor/Wall)', 'description' => 'Ceramic pieces used for flooring or walls.', 'category' => 'Construction Materials'],
            ['name' => 'Glass Panels', 'description' => 'Sheets of glass used in windows and doors.', 'category' => 'Construction Materials'],
            ['name' => 'Paint', 'description' => 'Colored liquid used to coat surfaces.', 'category' => 'Construction Materials'],

            // Tools and Equipment
            ['name' => 'Hammer', 'description' => 'Tool used to deliver impact on objects.', 'category' => 'Tools and Equipment'],
            ['name' => 'Drill', 'description' => 'Tool used to make holes in materials.', 'category' => 'Tools and Equipment'],
            ['name' => 'Screwdriver Set', 'description' => 'Tools used to drive screws.', 'category' => 'Tools and Equipment'],
            ['name' => 'Wrench Set', 'description' => 'Tools used to tighten or loosen nuts and bolts.', 'category' => 'Tools and Equipment'],
            ['name' => 'Pliers', 'description' => 'Tools used to hold objects firmly.', 'category' => 'Tools and Equipment'],
            ['name' => 'Saw (Hand Saw, Circular Saw)', 'description' => 'Tools used to cut materials.', 'category' => 'Tools and Equipment'],
            ['name' => 'Level Tool', 'description' => 'Tool used to check if a surface is horizontal.', 'category' => 'Tools and Equipment'],
            ['name' => 'Measuring Tape', 'description' => 'Flexible ruler used to measure size or distance.', 'category' => 'Tools and Equipment'],
            ['name' => 'Shovel', 'description' => 'Tool used to dig or move loose materials.', 'category' => 'Tools and Equipment'],
            ['name' => 'Wheelbarrow', 'description' => 'Single-wheeled cart used to carry loads.', 'category' => 'Tools and Equipment'],
            ['name' => 'Ladder', 'description' => 'Tool used for climbing up or down.', 'category' => 'Tools and Equipment'],
            ['name' => 'Scaffolding', 'description' => 'Temporary structure used to support work crews.', 'category' => 'Tools and Equipment'],
            ['name' => 'Concrete Mixer', 'description' => 'Machine used to mix concrete.', 'category' => 'Tools and Equipment'],
            ['name' => 'Welding Machine', 'description' => 'Device used to weld metal parts together.', 'category' => 'Tools and Equipment'],
            ['name' => 'Generator', 'description' => 'Machine that converts mechanical energy into electricity.', 'category' => 'Tools and Equipment'],
            ['name' => 'Safety Helmet', 'description' => 'Protective gear worn on the head.', 'category' => 'Tools and Equipment'],
            ['name' => 'Safety Goggles', 'description' => 'Eyewear that protects eyes from debris.', 'category' => 'Tools and Equipment'],
            ['name' => 'Gloves', 'description' => 'Handwear to protect hands from injury.', 'category' => 'Tools and Equipment'],
            ['name' => 'Ear Protection', 'description' => 'Devices to protect ears from loud noises.', 'category' => 'Tools and Equipment'],
            ['name' => 'High-Visibility Vest', 'description' => 'Vest worn to increase visibility.', 'category' => 'Tools and Equipment'],

            // Electrical Supplies
            ['name' => 'Electrical Wires', 'description' => 'Conductive wires used in electrical circuits.', 'category' => 'Electrical Supplies'],
            ['name' => 'Circuit Breakers', 'description' => 'Safety devices that stop current flow in circuits.', 'category' => 'Electrical Supplies'],
            ['name' => 'Switches', 'description' => 'Devices for opening or closing electrical circuits.', 'category' => 'Electrical Supplies'],
            ['name' => 'Sockets/Outlets', 'description' => 'Points where electrical devices are connected.', 'category' => 'Electrical Supplies'],
            ['name' => 'Electrical Panels', 'description' => 'Boards that house electrical components.', 'category' => 'Electrical Supplies'],
            ['name' => 'Conduits', 'description' => 'Tubes that protect and route electrical wiring.', 'category' => 'Electrical Supplies'],
            ['name' => 'Light Fixtures', 'description' => 'Housings for light bulbs.', 'category' => 'Electrical Supplies'],
            ['name' => 'Bulbs', 'description' => 'Electric light sources.', 'category' => 'Electrical Supplies'],
            ['name' => 'Transformers', 'description' => 'Devices that transfer electrical energy.', 'category' => 'Electrical Supplies'],
            ['name' => 'LED', 'description' => 'Devices that transfer electrical energy.', 'category' => 'Electrical Supplies'],

            // Plumbing Supplies
            ['name' => 'Pipes (PVC, Copper)', 'description' => 'Tubes used to convey water or other fluids.', 'category' => 'Plumbing Supplies'],
            ['name' => 'Fittings (Elbows, Tees, Couplings)', 'description' => 'Connectors for pipes.', 'category' => 'Plumbing Supplies'],
            ['name' => 'Faucets', 'description' => 'Valves for controlling the release of liquids.', 'category' => 'Plumbing Supplies'],
            ['name' => 'Valves', 'description' => 'Devices that regulate fluid flow.', 'category' => 'Plumbing Supplies'],
            ['name' => 'Water Heaters', 'description' => 'Devices that heat water.', 'category' => 'Plumbing Supplies'],
            ['name' => 'Toilets', 'description' => 'Sanitary fixtures for defecation and urination.', 'category' => 'Plumbing Supplies'],
            ['name' => 'Sinks', 'description' => 'Basins used for washing.', 'category' => 'Plumbing Supplies'],
            ['name' => 'Drainage Pipes', 'description' => 'Pipes that carry wastewater away.', 'category' => 'Plumbing Supplies'],
            ['name' => 'Water Pumps', 'description' => 'Machines that move water.', 'category' => 'Plumbing Supplies'],

            // Fixing and Fastening Items
            ['name' => 'Nails', 'description' => 'Small metal spikes used in construction.', 'category' => 'Fixing and Fastening Items'],
            ['name' => 'Screws', 'description' => 'Threaded fasteners used to hold objects together.', 'category' => 'Fixing and Fastening Items'],
            ['name' => 'Bolts and Nuts', 'description' => 'Fasteners used to hold objects together.', 'category' => 'Fixing and Fastening Items'],
            ['name' => 'Anchors', 'description' => 'Devices used to hold objects in place.', 'category' => 'Fixing and Fastening Items'],

            // Finishing Materials
            ['name' => 'Plaster', 'description' => 'Material used for coating walls and ceilings.', 'category' => 'Finishing Materials'],
            ['name' => 'Grout', 'description' => 'Material used to fill gaps between tiles.', 'category' => 'Finishing Materials'],
            ['name' => 'Sealant', 'description' => 'Material used to block fluid passage.', 'category' => 'Finishing Materials'],
            ['name' => 'Varnish', 'description' => 'Clear finish applied to wood.', 'category' => 'Finishing Materials'],
            ['name' => 'Wallpaper', 'description' => 'Decorative paper for walls.', 'category' => 'Finishing Materials'],
            ['name' => 'Floor Polish', 'description' => 'Product used to shine and protect floors.', 'category' => 'Finishing Materials'],
            ['name' => 'Carpeting Materials', 'description' => 'Materials used for floor coverings.', 'category' => 'Finishing Materials'],
            ['name' => 'CTF', 'description' => 'Materials used for floor coverings.', 'category' => 'Finishing Materials'],

            // Miscellaneous Items
            ['name' => 'Tarpaulin', 'description' => 'Waterproof material used as a cover.', 'category' => 'Miscellaneous Items'],
            ['name' => 'Paint Brushes/Rollers', 'description' => 'Tools used to apply paint.', 'category' => 'Miscellaneous Items'],
            ['name' => 'Buckets', 'description' => 'Containers for carrying liquids.', 'category' => 'Miscellaneous Items'],
            ['name' => 'Extension Cords', 'description' => 'Cords that extend power reach.', 'category' => 'Miscellaneous Items'],
            ['name' => 'Storage Containers', 'description' => 'Boxes or bins for storing items.', 'category' => 'Miscellaneous Items'],
            ['name' => 'Cleaning Supplies', 'description' => 'Products used for cleaning.', 'category' => 'Miscellaneous Items'],
            ['name' => 'Safety Signs', 'description' => 'Signs indicating safety information.', 'category' => 'Miscellaneous Items'],
            ['name' => 'Fire Extinguisher', 'description' => 'Device used to put out fires.', 'category' => 'Miscellaneous Items'],
        ];

        foreach ($items as $item) {
            Item::factory()->create([
                'name' => $item['name'],
                'description' => $item['description'],
                'item_category_id' => $categories[$item['category']]->id,
            ]);
        }
    }
}
