import Heading from '@/Components/Global/Heading';
import { exportReportAlreadyExist } from '@/Helpers/exportReport';
import { getResponseErrors } from '@/Helpers/responseErrors';
import Authenticated from '@/Layouts/Authenticated';
import { useEffect, useState } from 'react';
import toast from 'react-hot-toast';
import Form from './Form';

export default function Create({ auth, providers }) {
  const [fetchedFieldData, setFetchedFieldData] = useState('');
  const defaultData = {
    provider_id: '',
    file: '',
    received_at: new Date().toISOString().substr(0, 10),
    fieldMaps: [],
  };
  const [processing, setProcessing] = useState(false);
  const [data, setData] = useState(defaultData);
  const errors = {};

  const onHandleChange = (name, value) => {
    setData((prev) => ({ ...prev, [name]: value }));
  };

  useEffect(() => {
    return () => {
      setData(defaultData);
    };
  }, []);

  const checkRequiredFieldsMapped = () => {
    const tfnIndex = data.fieldMaps.findIndex(
      (item) => item.applicationField === 'toll_free_number'
    );

    const leadSkuIndex = data.fieldMaps.findIndex((item) => item.applicationField === 'lead_sku');

    if (tfnIndex < 0 && leadSkuIndex < 0) return true;
    return false;
  };

  const tfnIndex = data.fieldMaps.findIndex((item) => item.applicationField === 'toll_free_number');
  const leadSkuIndex = data.fieldMaps.findIndex((item) => item.applicationField === 'lead_sku');

  const saveAsDraft = (e) => {
    setProcessing(true);
    const formData = new FormData();
    formData.append('provider_id', data.provider_id);
    formData.append('fieldMaps', JSON.stringify(data.fieldMaps));

    axios
      .post(route('provider_file_fields.store'), formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      })
      .then(() => {
        setProcessing(false);
        axios.get(route('provider_file_fields.index', data.provider_id)).then((response) => {
          setFetchedFieldData(response.data);
        });
        toast.success('Provider File Fields Saved As Draft', { duration: 10000 });
      })
      .catch(() => {
        setProcessing(false);
        toast.error('Provider File Fields Saved As Draft', { duration: 5000 });
      });
  };

  const submit = (e) => {
    e.preventDefault();
    if (checkRequiredFieldsMapped()) {
      toast.error('TFN or LeadSKU field is required');
      return;
    }

    setProcessing(true);
    const formData = new FormData();
    formData.append('provider_id', data.provider_id);
    formData.append('received_at', data.received_at);
    formData.append('file', data.file);
    formData.append('fieldMaps', JSON.stringify(data.fieldMaps));

    axios
      .post(route('provider-files.store'), formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      })
      .then((res) => {
        e.target.reset();
        setData(defaultData);

        if (res.data?.alreadyExists) {
          exportReportAlreadyExist(res.data.alreadyExists);
        }
        setProcessing(false);
        toast.success(res.data.message, { duration: 10000 });
      })
      .catch((err) => {
        setProcessing(false);
        toast.error(getResponseErrors(err.response?.data), { duration: 5000 });
      });
  };

  return (
    <Authenticated auth={auth}>
      <div className="max-w-2xl mx-auto">
        <Heading className="mb-6">Import Provider File</Heading>

        <Form
          data={data}
          setData={onHandleChange}
          submit={submit}
          saveAsDraft={saveAsDraft}
          fetchedFieldData={fetchedFieldData}
          setFetchedFieldData={setFetchedFieldData}
          errors={errors}
          processing={processing}
          providers={providers}
        />
      </div>
    </Authenticated>
  );
}
