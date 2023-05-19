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
        <Label forInput="name" value="Name" required />
        <Input
          type="text"
          name="name"
          value={data.name}
          className="mt-1 block w-full"
          autoComplete="name"
          isFocused={true}
          handleChange={onHandleChange}
          required
        />
        <InputError message={errors.name} className="mt-2" />
      </div>

      <div className="mt-4">
        <Label forInput="delivery_method" value="Delivery Method" />
        <Input
          type="text"
          name="delivery_method"
          value={data.delivery_method}
          className="mt-1 block w-full"
          autoComplete="delivery_method"
          handleChange={onHandleChange}
        />
        <InputError message={errors.delivery_method} className="mt-2" />
      </div>

      <div className="mt-4">
        <Label forInput="response_type" value="Response Type" />
        <Input
          type="text"
          name="response_type"
          value={data.response_type}
          className="mt-1 block w-full"
          autoComplete="response_type"
          handleChange={onHandleChange}
        />
        <InputError message={errors.response_type} className="mt-2" />
      </div>

      <div className="mt-4">
        <Label forInput="timezone" value="Timezone" />
        <Input
          type="text"
          name="timezone"
          value={data.timezone}
          className="mt-1 block w-full"
          autoComplete="timezone"
          handleChange={onHandleChange}
        />
        <InputError message={errors.timezone} className="mt-2" />
      </div>

      <div className="mt-4">
        <Label forInput="delivery_days" value="Delivery Days" />
        <Input
          type="number"
          name="delivery_days"
          value={data.delivery_days}
          className="mt-1 block w-full"
          autoComplete="delivery_days"
          handleChange={onHandleChange}
        />
        <InputError message={errors.delivery_days} className="mt-2" />
      </div>

      <div className="mt-4">
        <Label forInput="auto_delivery" value="Auto Delivery" />
        <select
          name="auto_delivery"
          value={data.auto_delivery}
          className="mt-1 block w-full"
          onChange={onHandleChange}
        >
          <option value="">Select an option</option>
          <option value="1">Yes</option>
          <option value="0">No</option>
        </select>
        <InputError message={errors.auto_delivery} className="mt-2" />
      </div>

      <div className="mt-4">
        <Label forInput="file_naming_convention" value="File Naming Convention" />
        <Input
          type="text"
          name="file_naming_convention"
          value={data.file_naming_convention}
          className="mt-1 block w-full"
          autoComplete="file_naming_convention"
          handleChange={onHandleChange}
        />
        <InputError message={errors.file_naming_convention} className="mt-2" />
      </div>

      <div className="mt-4">
        <Label forInput="contact_name" value="Contact Name" />
        <Input
          type="text"
          name="contact_name"
          value={data.contact_name}
          className="mt-1 block w-full"
          autoComplete="contact_name"
          handleChange={onHandleChange}
        />
        <InputError message={errors.contact_name} className="mt-2" />
      </div>

      <div className="mt-4">
        <Label forInput="contact_email" value="Contact Email" />
        <Input
          type="email"
          name="contact_email"
          value={data.contact_email}
          className="mt-1 block w-full"
          autoComplete="contact_email"
          handleChange={onHandleChange}
        />
        <InputError message={errors.contact_email} className="mt-2" />
      </div>

      <div className="flex items-center justify-end mt-4">
        <Button className="ml-4" processing={processing}>
          {isUpdating ? 'Update' : 'Create'}
        </Button>
      </div>
    </form>
  );
}
