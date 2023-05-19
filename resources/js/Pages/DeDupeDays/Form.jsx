import Button from '@/Components/Global/Button';
import Input from '@/Components/Global/Input';
import InputError from '@/Components/Global/InputError';
import Label from '@/Components/Global/Label';
import Switch from '@/Components/Global/Switch';

export default function Form({ data, setData, submit, errors, processing, isUpdating = 0 }) {
  const onHandleChange = (event) => {
    setData(
      event.target.name,
      event.target.type === 'checkbox' ? event.target.checked : event.target.value
    );
  };

  return (
    <form onSubmit={submit}>
      <div>
        <Label forInput="days" value="Days" required />
        <Input
          type="number"
          name="days"
          value={data.days}
          className="mt-1 block w-full"
          autoComplete="days"
          isFocused={true}
          handleChange={onHandleChange}
          required
        />
        <InputError message={errors.days} className="mt-2" />
      </div>

      <div className="mt-4">
        <Label forInput="status" value="Status" />
        <Switch name="status" value={data.status} handleChange={onHandleChange} />
        <InputError message={errors.status} className="mt-2" />
      </div>

      <div className="flex items-center justify-end mt-4">
        <Button className="ml-4" processing={processing}>
          {isUpdating ? 'Update' : 'Create'}
        </Button>
      </div>
    </form>
  );
}
