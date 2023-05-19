import Button from '@/Components/Global/Button';
import Input from '@/Components/Global/Input';
import InputError from '@/Components/Global/InputError';
import Label from '@/Components/Global/Label';

export default function Form({ data, setData, submit, errors, processing, isUpdating = 0 }) {
  const onHandleChange = (event) => {
    setData(event.target.name, event.target.value);
  };
  return (
    <form onSubmit={submit}>
      <div>
        <Label forInput="state" value="State" required />
        <Input
          type="text"
          name="state"
          value={data.state}
          className="mt-1 block w-full"
          autoComplete="state"
          isFocused={true}
          handleChange={onHandleChange}
          required
        />
        <InputError message={errors.state} className="mt-2" />
      </div>

      <div>
        <Label forInput="area_code" value="Area Code" />
        <Input
          type="text"
          name="area_code"
          value={data.area_code || ''}
          className="mt-1 block w-full"
          autoComplete="area_code"
          isFocused={true}
          handleChange={onHandleChange}
        />
        <InputError message={errors.area_code} className="mt-2" />
      </div>

      <div>
        <Label forInput="zip_code" value="Zip Code" />
        <Input
          type="text"
          name="zip_code"
          value={data.zip_code || ''}
          className="mt-1 block w-full"
          autoComplete="zip_code"
          isFocused={true}
          handleChange={onHandleChange}
        />
        <InputError message={errors.zip_code} className="mt-2" />
      </div>

      <div className="mt-4">
        <InputError message={errors.status} className="mt-2" />
      </div>

      <div className="flex items-center justify-end mt-4">
        <InputError message={errors[0]}></InputError>
        <Button className="ml-4" processing={processing}>
          {isUpdating ? 'Update' : 'Create'}
        </Button>
      </div>
    </form>
  );
}
