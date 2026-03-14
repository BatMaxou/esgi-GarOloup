import Typography from '@/components/ui/atoms/typography';
import GlassPanel from '@/components/ui/atoms/glass-panel';

const Typographies = () => {
  return (
    <>
      <Typography tag="h2" variant="heading-2" bold className="mt-8 mb-4 block">
        Typographies
      </Typography>
      <div className="flex flex-wrap gap-8">
        <GlassPanel className="p-8">
          <ul className="flex flex-col flex-wrap gap-2">
            <li>
              <Typography variant="heading-1" special bold>
                GarOloup
              </Typography>
            </li>
            <li>
              <Typography variant="heading-1" bold>
                Heading 1
              </Typography>
            </li>
            <li>
              <Typography variant="heading-2" bold>
                Heading 2
              </Typography>
            </li>
            <li>
              <Typography variant="heading-3" bold>
                Heading 3
              </Typography>
            </li>
            <li>
              <Typography variant="subtitle" bold>
                Subtitle
              </Typography>
            </li>
          </ul>
        </GlassPanel>
        <GlassPanel className="p-8">
          <ul className="flex flex-col flex-wrap gap-2">
            <li>
              <Typography variant="body">Text Body</Typography>
            </li>
            <li>
              <Typography variant="body" bold>
                Text Body Bold
              </Typography>
            </li>
            <li>
              <Typography variant="body-sm">Text Body SM</Typography>
            </li>
            <li>
              <Typography variant="body-sm" bold>
                Text Body SM Bold
              </Typography>
            </li>
            <li>
              <Typography variant="body-xs">Text Body XS</Typography>
            </li>
            <li>
              <Typography variant="body-xs" bold>
                Text Body XS Bold
              </Typography>
            </li>
            <li>
              <Typography variant="button" bold>
                Text Button
              </Typography>
            </li>
            <li>
              <Typography underline>Text Body Underline</Typography>
            </li>
          </ul>
        </GlassPanel>
        <GlassPanel className="p-8">
          <ul className="flex flex-col flex-wrap gap-2">
            <li>
              <Typography bold textColor="controlled">
                Text color controlled
              </Typography>
            </li>
            <li>
              <Typography bold textColor="text">
                Text base color
              </Typography>
            </li>
            <li>
              <Typography bold textColor="light" className="bg-neutral-900">
                Text color light
              </Typography>
            </li>
            <li>
              <Typography bold textColor="primary">
                Text color primary
              </Typography>
            </li>
            <li>
              <Typography bold textColor="secondary">
                Text color secondary
              </Typography>
            </li>
            <li>
              <Typography bold textColor="accent">
                Text color accent
              </Typography>
            </li>
            <li>
              <Typography bold textColor="error">
                Text color error
              </Typography>
            </li>
            <li>
              <Typography bold textColor="success">
                Text color success
              </Typography>
            </li>
          </ul>
        </GlassPanel>
      </div>
    </>
  );
};

export default Typographies;
