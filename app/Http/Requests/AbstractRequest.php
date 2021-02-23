<?php declare(strict_types=1);

namespace App\Http\Requests;

use App\Exceptions\Handled\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Contracts\Validation\ValidatesWhenResolved;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidatesWhenResolvedTrait;
use Laravel\Lumen\Http\Redirector;

abstract class AbstractRequest extends Request implements ValidatesWhenResolved
{
    use  ValidatesWhenResolvedTrait;

    /** @var Container */
    protected $container;

    /** @var Redirector */
    protected $redirector;

    public function failedValidation(Validator $validator): void
    {
        throw new ValidationException($validator);
    }

    public function validated(): ?array
    {
        return $this->getValidatorInstance()->validate();
    }

    protected function getValidatorInstance(): Validator
    {
        $factory = $this->container->make(ValidationFactory::class);

        if (method_exists($this, 'validator')) {
            $validator = $this->container->call([$this, 'validator'], compact('factory'));
        } else {
            $validator = $this->createDefaultValidator($factory);
        }

        if (method_exists($this, 'withValidator')) {
            $this->withValidator($validator);
        }

        return $validator;
    }

    protected function createDefaultValidator(ValidationFactory $factory): Validator
    {
        return $factory->make(
            $this->validationData(),
            $this->container->call([$this, 'rules']),
            $this->messages(),
            $this->attributes()
        );
    }

    protected function validationData(): array
    {
        return $this->all() ?? [];
    }

    /**
     * Get custom messages for validator errors.
     * @return array
     */
    public function messages(): array
    {
        return [];
    }

    /**
     * Get custom attributes for validator errors.
     * @return array
     */
    public function attributes(): array
    {
        return [];
    }

    public function setRedirector(Redirector $redirector): AbstractRequest
    {
        $this->redirector = $redirector;
        return $this;
    }

    public function setContainer(Container $container): AbstractRequest
    {
        $this->container = $container;
        return $this;
    }

    protected function passesAuthorization(): bool
    {
        if (method_exists($this, 'authorize')) {
            return $this->container->call([$this, 'authorize']);
        }

        return true;
    }

    protected function failedAuthorization(): void
    {
        throw new AuthorizationException('This action is unauthorized.');
    }
}
