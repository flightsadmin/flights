<?php

namespace Flightsadmin\Flights\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

trait FileHandler
{
    public function spatiePermissionsInstall()
    {
        //Spatie Laravel Permission Installation
        if ($this->confirm('Do you want to Install Spatie Laravel Permission?', true, true)) {
            $this->permStubDir = __DIR__ . '/../../resources/install/permissions';
            $this->generatePermissionFiles();

            //Updating Routes
            $routeFile = base_path('routes/web.php');
            $routeData = file_get_contents($routeFile);
            $updatedData = $this->filesystem->get($routeFile);
            $spatieRoutes = 
            <<<ROUTES
            Route::view('flights', 'livewire.flights.index')->middleware('auth', 'role:super-admin|admin|user');
            Route::view('airlines', 'livewire.airlines.index')->middleware('auth', 'role:super-admin|admin|user');
            Route::view('registrations', 'livewire.registrations.index')->middleware('auth', 'role:super-admin|admin|user');
            Route::view('schedules', 'livewire.schedules.index')->middleware('auth', 'role:super-admin|admin');
            Route::view('delays', 'livewire.delays.index')->middleware('auth', 'role:super-admin|admin');
            Route::view('services', 'livewire.services.index')->middleware('auth', 'role:super-admin|admin');
            Route::view('permissions', 'livewire.permissions.index')->middleware('auth', 'role:super-admin|admin');
            Route::view('roles', 'livewire.roles.index')->middleware('auth', 'role:super-admin|admin');
            Route::view('users', 'livewire.users.index')->middleware('auth', 'role:super-admin|admin|user');
            ROUTES;
            $fileHook = "//Route Hooks - Do not delete//";

            if (!Str::contains($updatedData, trim($spatieRoutes))) {
                $UserModelContents = str_replace($fileHook, $fileHook . PHP_EOL . $spatieRoutes, $updatedData);
                $this->filesystem->put($routeFile, $UserModelContents);
                $this->warn($routeFile . ' Updated');
            }

            //Updating NavBar
            $layoutsFile = base_path('resources/views/components/layouts/app.blade.php');
            $layoutsData = $this->filesystem->get($layoutsFile);
            $spatieNavs  =
            <<<NAV
                                    @role('super-admin|admin')
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="bi bi-people-fill h5 text-info"></span>
                                            Admin
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-light">
                                            <li><a href="{{ url('/users') }}" wire:navigate class="nav-link"><i class="bi bi-people-fill h5 text-info"></i> Users </a></li>
                                            <li><a href="{{ url('/roles') }}" wire:navigate class="nav-link"><i class="bi bi-shield-shaded h5 text-info"></i> Roles </a></li>
                                            <li><a href="{{ url('/permissions') }}" wire:navigate class="nav-link"><i class="bi bi-person-fill-lock h5 text-info"></i> Permissions </a></li>
                                        </ul>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ url('/delays') }}" wire:navigate class="nav-link"><i class="bi bi-journal-code text-info h5"></i> Delay Codes </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ url('/services') }}" wire:navigate class="nav-link"><i class="bi bi-plus-slash-minus text-info h5"></i> Services </a>
                                    </li>
                                    @endrole
                                    <li class="nav-item">
                                        <a href="{{ url('/airlines') }}" wire:navigate class="nav-link"><i class="bi bi-database-add text-info h5"></i> Airlines </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ url('/registrations') }}" wire:navigate class="nav-link"><i class="bi bi-clock-history text-info h5"></i> Registrations </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ url('/flights') }}" wire:navigate class="nav-link"><i class="bi bi-airplane-engines-fill text-info h5"></i> Flights </a>
                                    </li>
            NAV;
            $spatieFileHook = "<!--Nav Bar Hooks - Do not delete!!-->";

            if (!Str::contains($layoutsData, $spatieNavs)) {
                $UserModelContents = str_replace($spatieFileHook, $spatieFileHook . PHP_EOL . $spatieNavs, $layoutsData);
                $this->filesystem->put($layoutsFile, $UserModelContents);
                $this->warn($layoutsFile . ' Updated');
            }

            //Updating Kernel
            $kernelFile = app_path('Http/Kernel.php');
            $kernelData = $this->filesystem->get($kernelFile);
            $kerneltemStub = "\t\t//Spatie Permission Traits\n\t\t'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class, \n\t\t'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class, \n\t\t'role_or_permission' => \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,\n\t\t//End Spatie Permission Trait";
            $kernelItemHook = (version_compare(app()->version(), '10.0.0', '>=')) ? 'protected $middlewareAliases = [' : 'protected $routeMiddleware = [';

            if (!Str::contains($kernelData, $kerneltemStub)) {
                $KernelContents = str_replace($kernelItemHook, $kernelItemHook . PHP_EOL . $kerneltemStub, $kernelData);
                $this->filesystem->put($kernelFile, $KernelContents);
                $this->warn('<info>' . $kernelFile . '</info> Updated');
            }

            // Updating User Model
            $userModelFile = app_path('Models/User.php');
            $fileData = $this->filesystem->get($userModelFile);
            $modelReplacements = [
                "class User extends Authenticatable\n{" => "\tuse HasRoles, SoftDeletes;",
                "namespace App\Models;\n"               => "use Spatie\Permission\Traits\HasRoles;\nuse Illuminate\Database\Eloquent\SoftDeletes;",
                "protected \$fillable = ["              => "\t\t'phone',\n\t\t'photo',\n\t\t'title',",
            ];
            
            foreach ($modelReplacements as $key => $value) {
                if (!Str::contains($fileData, $value)) {
                    $fileData = str_replace($key, $key . PHP_EOL . $value, $fileData);
                    $this->filesystem->put($userModelFile, $fileData);
                    $this->warn($userModelFile . ' Updated with <info>' . trim($value). '</info>');
                }
            }

            $this->line('');
            $this->warn('Publishing Laravel Permissions Files');
            Artisan::call('vendor:publish', ['--provider' => 'Spatie\Permission\PermissionServiceProvider'], $this->getOutput());
            $this->warn('Seeding the Database. Please wait...');
            Artisan::call('migrate:fresh', [], $this->getOutput());
            Artisan::call('optimize:clear', [], $this->getOutput());
            Artisan::call('db:seed', ['--class' => 'AdminDatabaseSeeder'], $this->getOutput());
            if ($this->confirm('Do you want to Seed Testing Data?', true, true)) {
                Artisan::call('db:seed', ['--class' => 'FlightsDatabaseSeeder'], $this->getOutput());
            }
        }
    }

    public function generatePermissionFiles()
    {
        $files = $this->filesystem->allFiles($this->permStubDir, true);
        foreach ($files as $file) {
            $filePath = $this->replace(Str::replaceLast('.stub', '', $file->getRelativePathname()));
            $fileDir = $this->replace($file->getRelativePath());

            if ($fileDir) {
                $this->filesystem->ensureDirectoryExists($fileDir);
            }
            $this->filesystem->put($filePath, $this->replace($file->getContents()));
            $this->warn('Generated file: <info>' . $filePath . '</info>');
        }
    }
}