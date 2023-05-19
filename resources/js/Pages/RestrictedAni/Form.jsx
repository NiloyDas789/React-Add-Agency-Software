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
        <Label forInput="restricted_ani" value="Restricted Ani" required />
        <Input
          type="text"
          name="restricted_ani"
          value={data.restricted_ani}
          className="mt-1 block w-full"
          autoComplete="restricted_ani"
          isFocused={true}
          handleChange={onHandleChange}
          required
        />
        <InputError message={errors.restricted_ani} className="mt-2" />
      </div>

      <div className="mt-4">
        <Label forInput="date" value="Restricted Ani Date" />
        <Input
          type="date"
          name="date"
          value={data.date}
          className="mt-1 block w-full"
          autoComplete="date"
          handleChange={onHandleChange}
        />
        <InputError message={errors.date} className="mt-2" />
      </div>

      <div className="mt-4">
        <Label forInput="reason" value="Restricted Ani Reason" />
        <Input
          type="text"
          name="reason"
          value={data.reason}
          className="mt-1 block w-full"
          autoComplete="reason"
          handleChange={onHandleChange}
        />
        <InputError message={errors.reason} className="mt-2" />
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
