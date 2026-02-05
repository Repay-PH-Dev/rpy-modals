Requirements:
    laravel, livewire, alpinejs, tailwind

add this code to root

```
@livewire('modal-manager')
```


```
php artisan modal:install
php artisan make:modal CreateFeeModal

```


usage  



```php


<?php

namespace App\Livewire;

use Repay\Modal\Traits\InteractsWithModal;

use App\Livewire\Modals\CreateFeeModal;
use Livewire\Component;

class ExamplePage extends Component
{
    use InteractsWithModal;

    public function render()

    {
        return view('livewire.fee-page');
    }


    public function openFeeModal()
    {
        $this->openModal(
            CreateFeeModal::class,
            [
                'userId' => null,
                'title' => 'Fee Modal ',
                'size' => '2xl',
            ]
        );
    }
}

```

in your view
```html

 <button
    wire:click="openFeeModal"
    class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600"
>
     Open Fee
</button>


```
