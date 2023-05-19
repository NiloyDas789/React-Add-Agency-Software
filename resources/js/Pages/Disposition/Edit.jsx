import Heading from '@/Components/Global/Heading';
import Authenticated from '@/Layouts/Authenticated';
import { useForm } from '@inertiajs/inertia-react';
import { useEffect } from 'react';
import toast from 'react-hot-toast';
import Form from './Form';

export default function Edit({ auth, disposition }) {
  const { data, setData, put, processing, errors, reset } = useForm({
    title: disposition?.title || '',
    status: disposition?.status || 0,
  });

  useEffect(() => {
    return () => {
      reset();
    };
  }, []);

  const submit = (e) => {
    e.preventDefault();

    put(route('dispositions.update', disposition.id), {
      onSuccess: () => {
        toast.success('Data dispositions Updated successfully.');
      },
    });
  };

  return (
    <Authenticated auth={auth}>
      <div className="max-w-2xl mx-auto">
        <Heading className="mb-6">Edit Disposition</Heading>
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
