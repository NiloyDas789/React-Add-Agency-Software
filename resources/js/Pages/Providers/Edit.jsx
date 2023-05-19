import Heading from '@/Components/Global/Heading';
import Authenticated from '@/Layouts/Authenticated';
import { useForm } from '@inertiajs/inertia-react';
import { useEffect } from 'react';
import toast from 'react-hot-toast';
import Form from './Form';

export default function Edit({ auth, provider }) {
  const { data, setData, put, processing, errors, reset } = useForm({
    name: provider?.name || '',
    delivery_method: provider?.delivery_method || '',
    response_type: provider?.response_type || '',
    timezone: provider?.timezone || '',
    delivery_days: provider?.delivery_days || '',
    auto_delivery: provider?.auto_delivery ?? '',
    file_naming_convention: provider?.file_naming_convention || '',
    contact_name: provider?.contact_name || '',
    contact_email: provider?.contact_email || '',
  });

  useEffect(() => {
    return () => {
      reset();
    };
  }, []);

  const submit = (e) => {
    e.preventDefault();

    put(route('providers.update', provider.id), {
      onSuccess: () => {
        toast.success('Provider updated successfully.');
      },
    });
  };

  return (
    <Authenticated auth={auth}>
      <div className="max-w-2xl mx-auto">
        <Heading className="mb-6">Edit Provider</Heading>
        <Form
          data={data}
          setData={setData}
          submit={submit}
          errors={errors}
          processing={processing}
          isUpdating={true}
        />
      </div>
    </Authenticated>
  );
}
